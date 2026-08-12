<?php

namespace Tests\Unit;

use App\Helpers\AppHelper;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Tests\TestCase;

class UploadSecurityTest extends TestCase
{
    private string $uploadDir;
    private array $created = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploadDir = public_path('upload');
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->created as $path) {
            if (is_link($path) || is_file($path)) {
                @unlink($path);
            }
        }
        parent::tearDown();
    }

    public function test_safe_delete_rejects_env_traversal(): void
    {
        $env = base_path('.env');
        $existed = is_file($env);
        $this->assertFalse(AppHelper::safeDeleteUpload('../.env'));
        $this->assertFalse(AppHelper::safeDeleteUpload('upload/../.env'));
        if ($existed) {
            $this->assertFileExists($env);
        }
    }

    public function test_safe_delete_rejects_index_php_traversal(): void
    {
        $index = public_path('index.php');
        $this->assertFileExists($index);
        $this->assertFalse(AppHelper::safeDeleteUpload('upload/../index.php'));
        $this->assertFalse(AppHelper::safeDeleteUpload('../public/index.php'));
        $this->assertFileExists($index);
    }

    public function test_safe_delete_rejects_absolute_and_foreign_paths(): void
    {
        $this->assertFalse(AppHelper::safeDeleteUpload('/public/upload/x.jpg'));
        $this->assertFalse(AppHelper::safeDeleteUpload('C:\\windows\\win.ini'));
        $this->assertFalse(AppHelper::safeDeleteUpload('http://example.com/x.jpg'));
        $this->assertFalse(AppHelper::safeDeleteUpload('storage/app/file.jpg'));
        $this->assertFalse(AppHelper::safeDeleteUpload('assets/icons/link-drop.png'));
        $this->assertFalse(AppHelper::safeDeleteUpload(''));
        $this->assertFalse(AppHelper::safeDeleteUpload(null));
    }

    public function test_safe_delete_removes_only_managed_upload_file(): void
    {
        $file = $this->uploadDir . DIRECTORY_SEPARATOR . 'safe-delete-test.jpg';
        file_put_contents($file, 'test');
        $this->created[] = $file;

        $this->assertTrue(AppHelper::safeDeleteUpload('upload/safe-delete-test.jpg'));
        $this->assertFileDoesNotExist($file);
    }

    public function test_safe_delete_does_not_follow_symlink_outside_upload(): void
    {
        $env = base_path('.env');
        if (!is_file($env) || PHP_OS_FAMILY === 'Windows') {
            $this->markTestSkipped('Symlink escape test needs .env on a Unix host.');
        }

        $link = $this->uploadDir . DIRECTORY_SEPARATOR . 'symlink-env.jpg';
        if (file_exists($link) || is_link($link)) {
            @unlink($link);
        }
        symlink($env, $link);
        $this->created[] = $link;

        $this->assertFalse(AppHelper::safeDeleteUpload('upload/symlink-env.jpg'));
        $this->assertFileExists($env);
        $this->assertTrue(is_link($link));
    }

    public function test_php_upload_is_rejected(): void
    {
        $file = UploadedFile::fake()->image('shell.php', 20, 20);
        $this->expectException(InvalidArgumentException::class);
        AppHelper::image_uploader($file);
    }

    public function test_jpg_php_double_extension_is_rejected(): void
    {
        $file = UploadedFile::fake()->image('shell.jpg.php', 20, 20);
        $this->expectException(InvalidArgumentException::class);
        AppHelper::image_uploader($file);
    }

    public function test_jpeg_content_with_php_filename_is_rejected(): void
    {
        $jpeg = UploadedFile::fake()->image('real.jpg', 20, 20);
        $file = new UploadedFile($jpeg->getRealPath(), 'image.php', 'image/jpeg', null, true);

        $this->expectException(InvalidArgumentException::class);
        AppHelper::image_uploader($file);
    }

    public function test_jpeg_content_with_png_filename_is_rejected(): void
    {
        $jpeg = UploadedFile::fake()->image('real.jpg', 20, 20);
        $file = new UploadedFile($jpeg->getRealPath(), 'image.png', 'image/jpeg', null, true);

        $this->expectException(InvalidArgumentException::class);
        AppHelper::image_uploader($file);
    }

    public function test_svg_upload_is_rejected(): void
    {
        $path = $this->uploadDir . DIRECTORY_SEPARATOR . 'probe.svg';
        file_put_contents($path, '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        $this->created[] = $path;

        $file = new UploadedFile($path, 'evil.svg', 'image/svg+xml', null, true);
        $this->expectException(InvalidArgumentException::class);
        AppHelper::image_uploader($file);
    }

    public function test_jpg_jpeg_and_png_upload_use_random_names(): void
    {
        $jpg = AppHelper::image_uploader(UploadedFile::fake()->image('a.jpg', 40, 40));
        $jpeg = AppHelper::image_uploader(UploadedFile::fake()->image('b.jpeg', 40, 40));
        $png = AppHelper::image_uploader(UploadedFile::fake()->image('c.png', 40, 40));
        $jpg2 = AppHelper::image_uploader(UploadedFile::fake()->image('a.jpg', 40, 40));

        $this->created[] = public_path($jpg);
        $this->created[] = public_path($jpeg);
        $this->created[] = public_path($png);
        $this->created[] = public_path($jpg2);

        $this->assertMatchesRegularExpression('#^upload/[0-9a-f-]{36}\.jpg$#', $jpg);
        $this->assertMatchesRegularExpression('#^upload/[0-9a-f-]{36}\.jpg$#', $jpeg);
        $this->assertMatchesRegularExpression('#^upload/[0-9a-f-]{36}\.png$#', $png);
        $this->assertNotSame($jpg, $jpg2);
        $this->assertFileExists(public_path($jpg));
        $this->assertFileExists(public_path($jpeg));
        $this->assertFileExists(public_path($png));
        $this->assertNotSame('upload/a.jpg', $jpg);
        $this->assertNotSame('upload/b.jpeg', $jpeg);
        $this->assertStringEndsNotWith('/a.jpg', $jpg);
        $this->assertStringEndsNotWith('/b.jpeg', $jpeg);
    }
}
