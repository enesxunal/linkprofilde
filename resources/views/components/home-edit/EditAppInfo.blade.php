<div class="modal fade" id="editAppInfo">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">{{__('Update App Info')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="POST" action="{{route('settings.global')}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">{{__('App Title')}}</label>
                        <input 
                            required
                            name="app_name"
                            class="form-control" 
                            value="{{$app->title}}"
                        >

                        @error('app_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label">{{__('App Description')}}</label>
                        <textarea 
                            rows="4" 
                            required 
                            name="description"
                            placeholder="Section Tiltle" 
                            class="form-control px-2 @error('description') is-invalid @enderror"
                        >{{$app->description}}</textarea>

                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 text-start">
                        <img 
                            alt=""
                            width="200px" 
                            id="currentAppLogo" 
                            src="{{asset($app->logo)}}"
                        >

                        <label style="display: block; margin-top: 12px">
                            {{__('Change Logo')}}
                        </label>
                        
                        <input 
                            id="newLogo" 
                            type="file" 
                            name="app_logo" 
                            class="form-control @error('app_logo') is-invalid @enderror"
                        >

                        @error('app_logo')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="mt-3 text-white form-control btn btn-primary">
                        {{__('Update')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("newLogo")?.addEventListener("change", function (e) {
        const reader = new FileReader();
        reader.readAsDataURL(e.target.files[0]);
        reader.addEventListener("load", () => {
            document.getElementById("currentAppLogo").src = reader.result;
        });
    });
</script>