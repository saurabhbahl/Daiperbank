{{-- dd($notification) --}}
<div class="modal" id="notificationModalPopup" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($notification && isset($notification->notification_information))
                    <p>{!! $notification->notification_information !!}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@section('js')
    <script>
        $(document).ready(function() {
            const notificaton = {!! json_encode($notification) !!};
            console.log(notificaton.disable);
            // Show modal if 'disable' is not set or is false
            if(!notificaton.disable){
                // Show the modal immediately
                $("#notificationModalPopup").show();

                // Close button functionality
                $("#close").click(function() {
                    $("#notificationModalPopup").hide();
                });
            }       
        });
    </script>
@endsection
