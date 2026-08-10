<div class="modal fade" id="editSection{{$sections->id}}">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Update {{$sections->name}} Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="POST" action="/home-section/edit/{{$sections->id}}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3 text-start">
                        <label class="form-label">{{__('Section Title')}}</label>
                        <input 
                            required 
                            name="section_title"
                            placeholder="Section Tiltle" 
                            value="{{$sections->title}}"
                            class="form-control px-2 @error('section_title') is-invalid @enderror"
                        >
                        @error('section_title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    @if($sections->description)
                        <div class="mb-3 text-start">
                            <label class="form-label">{{__('Section Description')}}</label>
                            <textarea 
                                rows="3" 
                                required 
                                name="description"
                                placeholder="Section Tiltle" 
                                class="form-control px-2 @error('description') is-invalid @enderror"
                            >
                                {{$sections->description}}
                            </textarea>

                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif

                    @if($sections->thumbnail)
                        <div class="mb-3 text-start">
                            <img 
                                alt=""
                                width="100%" 
                                id="currentThumbnail{{$sections->id}}" 
                                src="{{asset($sections->thumbnail)}}" 
                            >
                            <input name="current_thumbnail" value="{{$sections->thumbnail}}" hidden>

                            <label style="display: block; margin-top: 12px">
                                {{__('Change Thumbnail')}}
                            </label>
                            
                            <input 
                                type="file" 
                                name="new_thumbnail" 
                                id="newThumbnail{{$sections->id}}" 
                                class="form-control @error('new_thumbnail') is-invalid @enderror"
                            >

                            @error('new_thumbnail')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif

                    <button type="submit" class="mt-3 text-white form-control btn btn-primary">
                        {{__('Update')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("newThumbnail{{$sections->id}}")?.addEventListener("change", function (e) {
        const reader = new FileReader();
        reader.readAsDataURL(e.target.files[0]);
        reader.addEventListener("load", () => {
            document.getElementById("currentThumbnail{{$sections->id}}").src = reader.result;
        });
    });
</script>