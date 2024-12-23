@extends('layouts.app')

@section('content')

    <div class="breadcrumbs">
        <p class="crumb">Settings</p>
    </div>

    <div class="flex-auto flex justify-start content-stretch o-hidden">
        <div class="col-xs-12 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">
            <div class="notificationSetting">

                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-block notificationModalBtn" data-toggle="modal"
                    data-target="#notificationModal">
                    Popup Notification Settings
                </button>

                <!-- Modal -->
                <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel"
                    aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="notificationModalLabel">Popup Notification Settings</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    id="closeIcon">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('admin.settings.index') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group fieldWithCheckbox">
                                        <div class="inputField">
                                            <label for="notification_information">Add Information to Display </label>
                                            <textarea name="notification_information" id="notificationInput" class="form-control"
                                                value="{{ !empty($lastRecord->notification_information) ? $lastRecord->notification_information : '' }}"
                                                placeholder="Add information to display for agencies..." required>{{ !empty($lastRecord->notification_information) ? $lastRecord->notification_information : '' }}</textarea>
                                        </div>
                                        <div class="checkboxField">
                                            <label>
                                            <input type="checkbox" class="notificationCheckbox" name="disable" {{ !empty($lastRecord->disable) ? ($lastRecord->disable ? 'checked' : '') : '' }} />
                                            Disable
                                            </label>
                                            <p>If the above button is checked then users will not get information.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" value='{{ !empty($lastRecord->id) ? $lastRecord->id : "" }}'>
                                    <button type="submit" class="btn btn-primary saveNotificationBtn">Save</button>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-secondary" data-dismiss="modal"
                                    id="closeBtn">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- <script>
        $(document).ready(function() {

            if ("checked" in localStorage) {
                $("#notificationInput").attr("disabled", true);
                $(".saveNotificationBtn").attr("disabled", true);
            } else {
                $("#notificationInput").removeAttr("disabled", true);
                $(".saveNotificationBtn").removeAttr("disabled", true);
            }

            function checkboxUnchecked() {
                if ($(".notificationCheckbox").is(":not(:checked)")) {
                    $("#closeIcon").hide();
                    $("#closeBtn").hide();
                }
            }

            $(".notificationModalBtn").click(function() {
                checkboxUnchecked();
            });

            $('.notificationCheckbox').change(function(e) {
                if (this.checked) {
                    $("#notificationInput").attr("disabled", true);
                    $(".saveNotificationBtn").attr("disabled", true);
                    $("#closeIcon").show();
                    $("#closeBtn").show();
                } else {
                    localStorage.removeItem("checked");
                    $("#notificationInput").removeAttr("disabled", true);
                    $(".saveNotificationBtn").removeAttr("disabled", true);
                    $("#closeIcon").hide();
                    $("#closeBtn").hide();
                }
            });

        });

        function onClickBox() {
            let checked = $(".notificationCheckbox").is(":checked");
            localStorage.setItem("checked", checked);
        }

        function onReady() {
            let checked = "true" == localStorage.getItem("checked");
            $(".notificationCheckbox").prop('checked', checked);
            $(".notificationCheckbox").click(onClickBox);
        }

        $(document).ready(onReady);
    </script> -->
@endsection
