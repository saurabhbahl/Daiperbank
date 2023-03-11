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
                <p>{!! $notification->notification_information !!}</p>
            </div>
        </div>
    </div>
</div>

@section('js')
    <script>
        $(document).ready(function() {

            setTimeout(showModal, 1000);

            function showModal() {
                var is_modal_show = sessionStorage.getItem('alreadyShow');
                if (is_modal_show != 'alredy shown') {
                    $("#notificationModalPopup").show()
                    sessionStorage.setItem('alreadyShow', 'alredy shown');
                }
            }

            $("#close").click(function() {
                $("#notificationModalPopup").hide();
            })
        });
    </script>
@endsection
