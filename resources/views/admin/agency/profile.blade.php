@extends('layouts.app')

@section('content')

    <div class="breadcrumbs">
        <p class="crumb">Agency Information / Agency Contact Information</p>
    </div>
    <div class="flex-auto flex justify-start content-stretch o-hidden">
        <div class="col-xs-8 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">
            <div class="flex-auto flex justify-start content-stretch o-hidden">
                <div class="col-xs-10 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">
                    <div class="fg fs oy-auto agencyProfile">
                        <div class="agencyInfo">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Agency Name</th>
                                        <th>Agency Username</th>
                                        <th>Agency ID Prefix</th>
                                        <th>Contact Name</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Zip</th>
                                        <th>Agency Status</th>
                                        <th>Created at</th>
                                        <th>Updated at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td>{{ $agency->name }}</td>
                                        <td>{{ $agency->User->username }}</td>
                                        <!-- @foreach ($agency->User as $User)
                                            <td>{{ $User }}</td>
                                        @endforeach -->
                                        <td>{{ $agency->id_prefix }}</td>
                                        @foreach ($agency->contact as $contact)
                                            <td>{{ $contact->name }}</td>
                                            <td>{{ $contact->phone }}</td>
                                        @endforeach
                                        <td>
                                            {{ $agency->address }}
                                            {{ $agency->address_2 }}
                                        </td>
                                        <td>{{ $agency->city }}</td>
                                        <td>{{ $agency->state }}</td>
                                        <td>{{ $agency->zip }}</td>
                                        <td>{{ $agency->agency_status }}</td>
                                        <td>{{ $agency->created_at }}</td>
                                        <td>{{ $agency->updated_at }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xs-2 pb flex flex-column justify-start oy-auto hide-on-print">
                    <div class="fs-no fg-no pv4 bb b--black-20">
                        <button class="btn btn-block btn-success" onclick="window.print()">Print Agency Info</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stop
