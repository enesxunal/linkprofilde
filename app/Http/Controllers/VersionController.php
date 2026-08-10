<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use ZipArchive;

class VersionController extends Controller
{
    private $tmp_backup_dir = null;


    /**
     * Sürüm bilgisi. Otomatik güncelleme devre dışı olduğu için harici URL çağrılmaz.
     */
    public function checkVersion()
    {
        return [
            'version' => $this->getCurrentVersion(),
            'update_available' => false,
        ];
    }


    /**
     * Otomatik güncelleme devre dışı bırakıldı.
     * Yanlışlıkla veritabanı silinmesini önlemek için bu metod artık hiçbir işlem yapmaz.
     */
    public function updateVersion()
    {
        return back()->with('info', 'Otomatik güncelleme devre dışı bırakıldı. Veritabanı güvenliği için bu özellik kapatılmıştır.');
    }


    private function install($archive)
    {
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes 

        try {
            $execute_commands = false;
            $update_script = base_path() . '/' . config('installer.tmp_folder') . '/' . config('installer.script_file');

            $zip = new ZipArchive;
            if ($zip->open($archive) === TRUE) {
                $archive = substr($archive, 0, -4);

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $zip_item = $zip->statIndex($i);
                    $filename = $zip_item['name'];
                    $dirname = dirname($filename);

                    // Exclude files
                    if (substr($filename, -1) == '/' || dirname($filename) === $archive || substr($dirname, 0, 2) === '__') {
                        continue;
                    }

                    // Exclude the version.txt
                    if (strpos($filename, 'version.txt') !== false) {
                        continue;
                    }

                    if (substr($dirname, 0, strlen($archive)) === $archive) {
                        $dirname = substr($dirname, (-1) * (strlen($dirname) - strlen($archive) - 1));
                    }

                    //set new purify path for current file
                    $filename = $dirname . '/' . basename($filename);

                    if (!is_dir(base_path() . '/' . $dirname)) {
                        // Make NEW directory (if it already exists in the current version, continue...)
                        mkdir(base_path() . '/' . $dirname, 0755, true);
                    }

                    if (!is_dir(base_path() . '/' . $filename)) {
                        // Overwrite a file with its latest version
                        $contents = $zip->getFromIndex($i);

                        if (strpos($filename, 'upgrade.php') !== false) {
                            file_put_contents($update_script, $contents);
                            $execute_commands = true;
                        } else {
                            if (file_exists(base_path() . '/' . $filename)) {
                                $this->backup($filename); // backup current version
                            }

                            file_put_contents(base_path() . '/' . $filename, $contents, LOCK_EX);
                        }
                    }
                }

                $zip->close();
            } else {
                return false;
            }

            if ($execute_commands == true) {
                require_once($update_script);
                unlink($update_script);
            }

            File::delete($archive);
            File::deleteDirectory($this->tmp_backup_dir);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    private function download($filename)
    {
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes 

        $tmp_folder = base_path() . '/' . config('installer.tmp_folder');

        if (!is_dir($tmp_folder)) {
            File::makeDirectory($tmp_folder, $mode = 0755, true, true);
        }

        try {
            $local_file = $tmp_folder . '/' . $filename;
            $remote_file_url = config('installer.app_update_url') . '/' . $filename;
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $remote_file_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $update = curl_exec($curl);
            curl_close($curl);

            File::put($local_file, $update);
        } catch (\Exception $e) {
            return false;
        }

        return $local_file;
    }


    public function getCurrentVersion()
    {
        $version = File::get(base_path() . '/version.txt');
        return $version;
    }

    private function setCurrentVersion($version)
    {
        File::put(base_path() . '/version.txt', $version);
    }


    private function getLastVersion()
    {
        $curl = curl_init();
        $url = config('installer.app_version_url') . '/linkdrop.json';

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $last_version = curl_exec($curl);
        curl_close($curl);

        $last_version = json_decode($last_version, true);

        return $last_version;
    }


    private function backup($filename)
    {
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes 

        if (!isset($this->tmp_backup_dir)) {
            $this->tmp_backup_dir = base_path() . '/backup_' . date('Ymd');
        }

        $backup_dir = $this->tmp_backup_dir;
        if (!is_dir($backup_dir)) {
            File::makeDirectory($backup_dir, $mode = 0755, true, true);
        }

        if (!is_dir($backup_dir . '/' . dirname($filename))) {
            File::makeDirectory($backup_dir . '/' . dirname($filename), $mode = 0755, true, true);
        }

        File::copy(base_path() . '/' . $filename, $backup_dir . '/' . $filename); //to backup folder
    }


    private function recovery()
    {
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes 

        if (!isset($this->tmp_backup_dir)) {
            $this->tmp_backup_dir = base_path() . '/backup_' . date('Ymd');
        }

        try {
            $backup_dir = $this->tmp_backup_dir;
            $backup_files = File::allFiles($backup_dir);
            foreach ($backup_files as $file) {
                $filename = (string)$file;
                $filename = substr($filename, (strlen($filename) - strlen($backup_dir) - 1) * (-1));
                File::copy($backup_dir . '/' . $filename, base_path() . '/' . $filename); //to respective folder
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function changeEnv($data = array())
    {
        if (count($data) > 0) {
            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/(\r\n|\n|\r)/', $env);

            // Loop through given data
            foreach ((array) $data as $key => $value) {

                // Loop through .env-data
                foreach ($env as $env_key => $env_value) {

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if ($entry[0] == $key) {
                        // If yes, overwrite it with the new one
                        if ($value !== null) {

                            $env[$env_key] = $key . "=" . $value;
                        }
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);

            return true;
        } else {
            return false;
        }
    }
}
