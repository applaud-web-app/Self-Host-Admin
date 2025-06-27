@extends('admin.layout.app')

@push('styles')
<link href="{{ asset('css/iziToast.css') }}" rel="stylesheet">
<script src="{{ asset('js/iziToast.js') }}"></script>
<style>
    .error {
        color: red;
        font-size: 0.875em;
    }
</style>
@endpush

@section('content')
<section class="content-body">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Edit Coupon</h2>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="addCouponForm" action="{{$data['encryptUrl']}}" method="POST" autocomplete="off">
                            @csrf
                            <div class="row">
                                <!-- Coupon Code -->
                                <div class="col-lg-6 col-md-6 col-12 mb-3">
                                    <div class="form-group">
                                        <label for="code">Coupon Code <span class="text-danger">*</span></label>
                                        <div class="d-flex">
                                            <input type="text" class="form-control rounded-0 text-uppercase" name="code" id="code" placeholder="Enter coupon code" value="{{$data['coupon']->coupon_code}}" required>
                                            <button class="btn btn-primary rounded-0" id="genrateCode" type="button"><i class="fas fa-sync"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Discount Type -->
                                <div class="col-lg-6 col-md-6 col-12 mb-3">
                                    <div class="form-group">
                                        <label for="discount_type">Discount Type <span class="text-danger">*</span></label>
                                        <select class="form-select form-control" name="discount_type" id="discount_type" required>
                                            <option value="" disabled selected>Select Discount Type</option>
                                            <option value="percentage" {{$data['coupon']->discount_type == "percentage" ? 'selected' : ''}} >Percentage (%)</option>
                                            <option value="fixed" {{$data['coupon']->discount_type == "fixed" ? 'selected' : ''}} >Fixed Amount (₹)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Discount Amount -->
                                <div class="col-lg-6 col-md-6 col-12 mb-3">
                                    <div class="form-group">
                                        <label for="discount_amount">Discount Amount (₹)<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="discount_amount" id="discount_amount" placeholder="Enter discount amount" min="1" step="1" value="{{round($data['coupon']->discount_amount)}}" required>
                                    </div>
                                </div>

                                <!-- Start Date -->
                                <div class="col-lg-6 col-md-6 col-12 mb-3">
                                    <div class="form-group">
                                        <label for="expiry_date">Expiry Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="expiry_date" value="{{$data['coupon']->expiry_date}}" id="expiry_date" required>
                                    </div>
                                </div>

                                <!-- Usage Type -->
                                <div class="col-lg-6 col-md-6 col-12 mb-3">
                                    <div class="form-group">
                                        <label for="usage_type">Usage Type <span class="text-danger">*</span></label>
                                        <select class="form-select form-control" name="usage_type" id="usage_type" required>
                                            <option value="" disabled selected>Select Usage Type</option>
                                            <option value="single" {{$data['coupon']->usage_type == "single" ? 'selected' : '' }}>Only Once</option>
                                            <option value="multiple" {{$data['coupon']->usage_type == "multiple" ? 'selected' : '' }}>Multiple</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Usage Limit -->
                                <div class="col-lg-6 col-md-6 col-12 mb-3 {{$data['coupon']->usage_type == "multiple" ? '' : 'd-none' }}" id="usageLimit">
                                    <div class="form-group">
                                        @php $limit = ""; @endphp
                                        @if ($data['coupon']->usage_type == "multiple")
                                            @php $limit = $data['coupon']->usage_limit; @endphp
                                        @endif
                                        <label for="usage_limit">Usage Limit <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" value="{{$limit}}" name="usage_limit" id="usage_limit" placeholder="Enter usage limit" min="2" required>
                                    </div>
                                </div>

                                <!-- Short Description -->
                                <div class="col-lg-12 col-md-12 col-12 mb-3">
                                    <div class="form-group">
                                        <label for="description">Short Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="description" id="description" placeholder="Enter short description" maxlength="225" style="height: 100px" required>{{$data['coupon']->description}}</textarea>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-lg-12 col-md-12 col-12 mb-3">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <div class="custom-radio justify-content-start">
                                            <label class="w-auto d-inline-block me-3" for="enable">
                                                <input type="radio" name="status" id="enable" value="1" {{$data['coupon']->status == 1 ? 'checked' : '' }}>
                                                <span>Enable</span>
                                            </label>
                                            <label class="w-auto d-inline-block" for="disable">
                                                <input type="radio" name="status" id="disable" value="0" {{$data['coupon']->status == 0 ? 'checked' : '' }}>
                                                <span>Disable</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit and Reset Buttons -->
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary mt-3" id="submitButton">Submit</button>
                                    <button type="reset" class="btn btn-link text-danger mt-3">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        // Initialize form validation
        $("#addCouponForm").validate({
            rules: {
                code: {
                    required: true,
                    nowhitespace: true, // Custom rule for no whitespace
                    remote: {
                        url: "{{$data['checkCoupon']}}",
                        type: "POST",
                        data: {
                            _token: "{{csrf_token()}}",
                            code: function () {
                                return $("#code").val();
                            }
                        },
                        dataFilter: function (response) {
                            const res = JSON.parse(response);
                            return res.status === true;
                        }
                    }
                },
                discount_type: {
                    required: true
                },
                discount_amount: {
                    required: true,
                    number: true,
                    min: 1,
                    max: function () {
                        return $("#discount_type").val() === "percentage" ? 100 : 99999;
                    }
                },
                expiry_date: {
                    required: true,
                    date: true,
                    min: function () {
                        const tomorrow = new Date();
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        return tomorrow.toISOString().split('T')[0];
                    }
                },
                usage_type: {
                    required: true
                },
                usage_limit: {
                    required: function () {
                        return $("#usage_type").val() === "multiple";
                    },
                    number: true,
                    min: 2
                },
                description: {
                    required: true,
                    minlength: 10,
                    maxlength: 225
                },
                status: {
                    required: true
                }
            },
            messages: {
                code: {
                    required: "Coupon code is required.",
                    nowhitespace: "Coupon code cannot contain spaces.",
                    remote: "Coupon code already exists."
                },
                discount_type: {
                    required: "Discount type is required."
                },
                discount_amount: {
                    required: "Discount amount is required.",
                    number: "Enter a valid number.",
                    min: "Discount amount must be at least 1.",
                    max: "Percentage discount cannot exceed 100."
                },
                expiry_date: {
                    required: "Expiry date is required.",
                    date: "Enter a valid date.",
                    min: "Expiry date must be in the future."
                },
                usage_type: {
                    required: "Usage type is required."
                },
                usage_limit: {
                    required: "Usage limit is required for multiple usage types.",
                    number: "Enter a valid number.",
                    min: "Usage limit must be at least 2."
                },
                description: {
                    required: "Short description is required.",
                    minlength: "Description must be at least 10 characters.",
                    maxlength: "Description cannot exceed 225 characters."
                },
                status: {
                    required: "Status is required."
                }
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") === "status") {
                    error.insertAfter(element.closest('.custom-radio'));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                $("#submitButton").prop("disabled", true).text("Processing...");
                form.submit();
            }
        });

        // Custom rule for no whitespace in coupon code
        $.validator.addMethod("nowhitespace", function (value, element) {
            return this.optional(element) || /^\S+$/.test(value);
        }, "No spaces are allowed.");

        // Handle discount type change
        $(document).on('change', '#discount_type', function () {
            const discountType = $(this).val();
            const discountInput = $('#discount_amount');
            if (discountType === 'percentage') {
                discountInput.attr('max', 100); // Limit to 100 for percentage
                discountInput.attr('placeholder', 'Enter a percentage (max 100)');
            } else {
                discountInput.attr('max', 99999); // Limit to 99999 for fixed
                discountInput.attr('placeholder', 'Enter a fixed amount (max ₹99999)');
            }
        });

        // Show/Hide Usage Limit Field
        $(document).on('change', '#usage_type', function () {
            const usageType = $(this).val();
            $('#usageLimit').toggleClass('d-none', usageType !== "multiple");
        });

        // Pre-fill Minimum Expiry Date
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        $('#expiry_date').attr('min', tomorrow.toISOString().split('T')[0]);

        // Validate Coupon Code on Input
        $(document).on('input', '#code', function () {
            const couponVal = $(this).val();
            if (couponVal.length === 10) {
                checkCoupon(couponVal);
            }
        });

        // Generate Coupon Code and Check Uniqueness
        $(document).on('click', '#genrateCode', function () {
            const randStr = "09876QAZWSXEDCRFVTGBYHNUJMIKOLP54321";
            const generateRandomCode = (length) => Array.from({ length }, () => randStr[Math.floor(Math.random() * randStr.length)]).join('');
            const generatedCode = generateRandomCode(6) + Math.floor(1000 + Math.random() * 9000);
            $('#code').val(generatedCode).trigger('input');
        });

        // Function to Check Coupon Code Uniqueness
        function checkCoupon(code) {
            if (!code || code.length !== 10) {
                iziToast.error({
                    title: 'Error',
                    message: 'Code length must be exactly 10 characters.',
                    position: 'topRight',
                });
                return;
            }

            $.ajax({
                url: "{{$data['checkCoupon']}}",
                method: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    code: code
                },
                success: function (response) {
                    const type = response.status === true ? 'success' : 'warning';
                    iziToast[type]({
                        title: type.charAt(0).toUpperCase() + type.slice(1),
                        message: response.message,
                        position: 'topRight',
                    });
                    $('#code-error').remove();
                    $('#code').css('color','#000');
                },
                error: function (error) {
                    iziToast.error({
                        title: 'Error',
                        message: error.responseJSON?.message || 'An unexpected error occurred',
                        position: 'topRight',
                    });
                }
            });
        }
    });
</script>
@endpush