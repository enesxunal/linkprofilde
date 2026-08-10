<div class="modal fade" id="editSectionList{{$sections->id}}">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    {{__('Update')}} 
                    {{$sections->name}} 
                    {{__('Section List Items')}}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form method="POST" action="/home-section/edit-list/{{$sections->id}}">
                    @csrf
                    @method('PUT')
                    
                    <?php $counter = 0; ?>
                    @foreach(json_decode($sections->section_list) as $list)
                        <?php
                            $counter++;
                            $encode = json_encode($list);
                            $item = json_decode($encode, true);
                        ?>
                        @if($item['content'] && $item['url'])
                            <div class="mb-3 text-start">
                                <div class="input-group rounded-0">
                                    <span 
                                        class="input-group-text" 
                                        style="width: 86px; border-radius: 5px 0px 0px 0px;"
                                    >
                                        {{__('Content')}}
                                    </span>
                                    <input 
                                        required 
                                        name="content{{$counter}}"
                                        value="{{$item['content']}}"
                                        class="form-control px-2 @error('section_title') is-invalid @enderror"
                                        style="border-radius: 0px 5px 0px 0px;"
                                    >
                                </div>

                                <input hidden name="icon{{$counter}}" value="{{$item['icon']}}">

                                <div class="input-group">
                                    <span 
                                        class="input-group-text" 
                                        style="width: 86px; border-radius: 0px 0px 0px 5px; border-top: 0"
                                    >
                                        {{__('Link Url')}}
                                    </span>
                                    <input 
                                        required 
                                        name="url{{$counter}}"
                                        value="{{$item['url']}}"
                                        class="form-control px-2 @error('section_title') is-invalid @enderror"
                                        style="border-radius: 0px 0px 5px 0px; border-top: 0"
                                    >
                                </div>
                            </div>
                        @else
                            @if($item['url'])
                                <div class="mb-3 text-start">
                                    <div class="input-group rounded-0">
                                        <span 
                                            class="input-group-text" 
                                            style="width: 86px;"
                                        >
                                            {{__('Link Url')}}
                                        </span>
                                        <input 
                                            required 
                                            name="url{{$counter}}"
                                            value="{{$item['url']}}"
                                            class="form-control px-2 @error('section_title') is-invalid @enderror"
                                        >
                                    </div>

                                    <input hidden name="icon{{$counter}}" value="{{$item['icon']}}">
                                    <input hidden name="content{{$counter}}" value="{{$item['content']}}">
                                </div>
                            @else
                                <div class="mb-3 text-start">
                                    <div class="input-group rounded-0">
                                        <span 
                                            class="input-group-text" 
                                            style="width: 86px;"
                                        >
                                            {{__('Content')}}
                                        </span>
                                        <input 
                                            required 
                                            name="content{{$counter}}"
                                            value="{{$item['content']}}"
                                            class="form-control px-2 @error('section_title') is-invalid @enderror"
                                        />
                                    </div>

                                    <input hidden name="icon{{$counter}}" value="{{$item['icon']}}" />
                                    <input hidden name="url{{$counter}}" value="{{$item['url']}}" />
                                </div>
                            @endif
                        @endif

                    @endforeach

                    <button type="submit" class="mt-3 text-white form-control btn btn-primary">
                        {{__('Update')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>