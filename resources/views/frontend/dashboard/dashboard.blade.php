@extends('layouts.frontend.app')

@push('header')
    <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/plugins/iCheck/square/blue.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css"
        integrity="sha512-8D+M+7Y6jVsEa7RD6Kv/Z7EImSpNpQllgaEIQAtqHcI0H6F4iZknRj0Nx1DCdB+TwBaS+702BGWYC0Ze2hpExQ=="
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/0.7.8/themes/black/pace-theme-flash.min.css"
        integrity="sha512-0c1cb0LYXVvb9L459008ryNuWW7NuZEFY0ns6fAOfpJhHnTX7Db2vbSrjaLgvUpcl+atb3hkawh2s+eEE3KaLQ=="
        crossorigin="anonymous" />
@endpush

@section('content')
    <section class="contact inner-sec-pad">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="contact-box mt-5">
                        <div class="contact-right">
                            <form action="#" method="post" id="profile-form">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6 col-12 pl-2">
                                        <label for="input-1" class="control-label"> Name:</label>
                                        <input type="text" placeholder="Enter first name" class="form-control"
                                            value="{{ $user_details->name }}" name="name" readonly>
                                        @error('name')
                                            <div class="p-3 mb-2 text-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-12 pl-2">
                                        <label for="input-1" class="control-label"> Email:</label>
                                        <input type="text" placeholder="Enter last name" class="form-control"
                                            value="{{ $user_details->email }}" name="email" readonly>
                                        @error('email')
                                            <div class="p-3 mb-2 text-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-12 pl-2">
                                        <label for="input-1" class="control-label"> Role:</label>
                                        <input type="text" placeholder="Enter Email" readonly class="form-control"
                                            value="{{ $user_details->role_id }}" name="role_id" readonly>
                                        @error('role_id')
                                            <div class="p-3 mb-2 text-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6 col-12 pl-2">
                                        <label for="input-1" class="control-label"> Phone:</label>
                                        <input type="text" placeholder="Enter phone" class="form-control"
                                            value="{{ $user_details->mobile }}" name="mobile" readonly>
                                        @error('mobile')
                                            <div class="p-3 mb-2 text-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                {{-- <div>
    <button type="submit" class="btn btn-info" value="Submit">Update</button>
    </div> --}}
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script src="https://adminlte.io/themes/AdminLTE/plugins/iCheck/icheck.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww=="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/0.7.8/pace.min.js"
        integrity="sha512-t3TewtT7K7yfZo5EbAuiM01BMqlU2+JFbKirm0qCZMhywEbHZWWcPiOq+srWn8PdJ+afwX9am5iqnHmfV9+ITA=="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"
        integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw=="
        crossorigin="anonymous"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
