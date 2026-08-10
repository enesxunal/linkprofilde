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
                <form method="POST" action="/home-section/edit-list">
                    @csrf
                    @method('PUT')
                    

                    <button type="submit" class="mt-3 text-white form-control btn btn-primary">
                        {{__('Update')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>