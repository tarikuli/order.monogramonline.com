<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>View order details</title>
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
    @include('includes.header_menu')
    <div class = "container apply-margin-top-bottom">
        <ol class = "breadcrumb">
            <li><a href = "{{url('/')}}">Home</a></li>
            <li><a href = "{{url('orders/list')}}">Orders</a></li>
            <li class = "active">Details</li>
        </ol>
        <div class = "row">
            <div class = "form-group col-xs-3">
                <label class = "control-label" for = "date">Date :</label>
                {!! Form::text('date', \Monogram\Helper::dateTransformer($order->order_date), ['id' => 'date', 'class' => 'form-control', 'disabled']) !!}
            </div>
            <div class = "form-group col-xs-3">
                <label class = "control-label" for = "status">Status:</label>
                {!! Form::select('status', $statuses, \App\Status::find($order->order_status)->status_code, ['class' => 'form-control', 'id' => 'status']) !!}
            </div>
            <div class = "form-group col-xs-3">
                <label class = "control-label" for = "order">Order#: {{$order->short_order}}</label>
                <select class = "form-control" id = "order">
                    <option value = "0" selected>Reg</option>
                    <option value = "1">Phone</option>
                    <option value = "100">REPAIR</option>
                    <option value = "101">REDO</option>
                    <option value = "2">W/H</option>
                    <option value = "3">D/S</option>
                    <option value = "4">FB</option>
                    <option value = "5">Mobile</option>
                    <option value = "6">FBA</option>
                </select>
            </div>
            <div class = "form-group col-xs-3">
                <label class = "control-label" for = "customer">Customer #:</label>
                {!! Form::text('customer_id', $order->customer->id, ['id' => 'customer', 'class' => 'form-control']) !!}
            </div>
        </div>
        <div class = "row">
            <div class = "col-xs-6">
                <div class = "col-xs-12 text-center"><h4 class = "text-primary">Ship To</h4></div>
                <form class = "form-horizontal" role = "form">
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "company_name">Company Name:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('company_name', $order->customer->ship_company_name, ['id' => 'company_name', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "first_name">First Name:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('first_name', $order->customer->ship_first_name, ['id' => 'first_name', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "last_name">Last Name:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('last_name', $order->customer->ship_last_name, ['id' => 'last_name', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "shipping_address_1">Address 1:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('shipping_address_1', $order->customer->ship_address_1, ['id' => 'shipping_address_1', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "shipping_address_2">Address 2:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('shipping_address_2', $order->customer->ship_address_2, ['id' => 'shipping_address_2', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "ship_city">City:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('ship_city', $order->customer->ship_city, ['id' => 'ship_city', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "ship_state">State:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('ship_state', $order->customer->ship_state, ['id' => 'ship_state', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "ship_zip">Zip:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('ship_zip', $order->customer->ship_zip, ['id' => 'ship_zip', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "ship_country">Country:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('ship_country', $order->customer->ship_country, ['id' => 'ship_country', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "Phone">Phone:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('ship_phone', $order->customer->ship_phone, ['id' => 'company_name', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "Phone">Email:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('ship_email', $order->customer->ship_email, ['id' => 'ship_email', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </form>
            </div>
            <div class = "col-xs-6">
                <div class = "col-xs-12 text-center"><h4 class = "text-primary">Bill To</h4></div>
                <form class = "form-horizontal" role = "form">
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_company_name">Company Name:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_company_name', $order->customer->bill_company_name, ['id' => 'bill_company_name', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_first_name">First Name:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_first_name', $order->customer->bill_first_name, ['id' => 'bill_first_name', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_last_name">Last Name:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_last_name', $order->customer->bill_last_name, ['id' => 'bill_last_name', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_address_1">Address 1:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_address_1', $order->customer->bill_address_1, ['id' => 'bill_address_1', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_address_2">Address 2:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_address_2', $order->customer->bill_address_2, ['id' => 'bill_address_2', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_city">City:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_city', $order->customer->bill_city, ['id' => 'bill_city', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_state">State:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_state', $order->customer->bill_state, ['id' => 'bill_state', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_zip">Zip:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_zip', $order->customer->bill_zip, ['id' => 'bill_zip', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_country">Country:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_country', $order->customer->bill_country, ['id' => 'bill_country', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_phone">Phone:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_phone', $order->customer->bill_phone, ['id' => 'bill_phone', 'class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "bill_email">Email:</label>
                        <div class = "col-xs-6">
                            {!! Form::text('bill_email', $order->customer->bill_email, ['id' => 'bill_email', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class = "row">
            <div class = " form-group col-xs-3">
                <label class = "control-label" for = "Amount">Amount :</label>
                {!! Form::text('total', $order->total, ['id' => 'total', 'class' => 'form-control']) !!}
            </div>
            <div class = "form-group col-xs-3">
                <label class = "control-label" for = "paid">Paid:</label>
                {!! Form::select('paid', ['Yes', 'No'], $order->paid, ['id' => 'paid', 'class' => 'form-control']) !!}
            </div>
            {{--<div class = " form-group col-xs-2">
                <label class = "control-label" for = "Paid">Paid :</label>
                <label class = "form-control" id = "Paid">$123</label>
            </div>--}}
            <div class = "form-group col-xs-3">
                <label class = "control-label" for = "tax_calculation">Auto tax calculation:</label>
                {!! Form::select('tax_calculation', ['Yes', 'No'], $order->paid, ['id' => 'tax_calculation', 'class' => 'form-control']) !!}
            </div>
            <div class = " form-group col-xs-3">
                <label class = "control-label" for = "shipping_method">Ship Via:</label>
                {!! Form::select('shipping_method', $shipping_methods, $order->customer->shipping, ['id' => 'shipping_method', 'class' => 'form-control']) !!}
            </div>
        </div>

        <div class = "row">
            <div class = " form-group col-xs-3">
                <label class = "control-label" for = "order_comments">Customer comment:</label>
                {!! Form::textarea('order_comments', $order->order_comments, ['id' => 'order_comments', 'class' => 'form-control', 'rows' => '2']) !!}
            </div>
            <div class = " form-group col-xs-3">
                <label class = "control-label" for = "Gift message">Gift message:</label>
                <textarea class = "form-control" id = "Gift message"></textarea>

            </div>
            <div class = "form-group col-xs-3">
                <label class = "control-label " for = "email">Email:</label>
                {!! Form::text('email', $order->customer->ship_email, ['id' => 'email', 'class' => 'form-control']) !!}
            </div>
            <div class = " form-group col-xs-2">
                <label class = "control-label" for = "payment_method">Payment:</label>
                {!! Form::text('payment_method', $order->payment_method, ['id' => 'payment_method', 'class' => 'form-control']) !!}
            </div>
        </div>
        <div class = "row">
            <table class = "table table-bordered">
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Quantity</th>
                    <th>Inv</th>
                    <th>Each</th>
                    <th>Options</th>
                    {{--<th>D/Shipper<br><a href = "#">(Transmit)</a></th>
                    <th>B/O</th>--}}
                </tr>
                @foreach($order->items as $item)
                    <tr>
                        <td><a href = "#">{{$item->item_description}}</a></td>
                        <td><a href = "#">{{$item->item_code}}</a></td>
                        <td>{{$item->item_quantity}}</td>
                        <td></td>
                        <td>{{$item->item_unit_price}}</td>
                        <td>{!! Form::textarea('item_option', \Monogram\Helper::jsonTransformer($item->item_option), ['id' => 'item_option', 'class' => 'form-control', 'rows' => '4']) !!}</td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class = "row">
            <div class = "col-xs-offset-7 col-xs-5">
                <form class = "form-horizontal" role = "form">
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "sub_total">Subtotal:</label>
                        <div class = "col-xs-9">
                            <div class = "input-group">
                                <span class = "input-group-addon">$</span>
                                {!! Form::text('sub_total', sprintf("%02.2f",$order->sub_total), ['id' => 'sub_total', 'class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "discount">Coupon:</label>
                        <div class = "col-xs-9">
                            <div class = "input-group">
                                <span class = "input-group-addon">$</span>
                                {!! Form::text('discount', sprintf("%02.2f",$order->coupon_value), ['id' => 'discount', 'class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "gift_wrap_cost">Gift Wrap:</label>
                        <div class = "col-xs-9">
                            <div class = "input-group">
                                <span class = "input-group-addon">$</span>
                                {!! Form::text('gift_wrap_cost', sprintf("%02.2f",$order->gift_wrap_cost), ['id' => 'gift_wrap_cost', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "shipping_cost">Shipping:</label>
                        <div class = "col-xs-9">
                            <div class = "input-group">
                                <span class = "input-group-addon">$</span>
                                {!! Form::text('shipping_cost', sprintf("%02.2f",$order->shipping_charge), ['id' => 'shipping_cost', 'class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "insurance">Insurance:</label>
                        <div class = "col-xs-9">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                {!! Form::text('insurance', sprintf("0.00"), ['id' => 'insurance', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "adjustments">Adjustments:</label>
                        <div class = "col-xs-9">
                            <div class = "input-group">
                                <span class = "input-group-addon">$</span>
                                {!! Form::text('adjustments', sprintf("%02.2f",$order->adjustments), ['id' => 'adjustments', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "tax">Tax:</label>
                        <div class = "col-xs-9">
                            <div class = "input-group">
                                <span class = "input-group-addon">$</span>
                                {!! Form::text('tax', sprintf("%02.2f",$order->tax_charge), ['id' => 'tax', 'class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                    </div>
                    <div class = "form-group">
                        <label class = "control-label col-xs-3" for = "total">Total:</label>
                        <div class = "col-xs-9">
                            <div class = "input-group">
                                <span class = "input-group-addon">$</span>
                                {!! Form::text('total', sprintf("%02.2f",$order->total), ['id' => 'order_total', 'class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                    </div>
                    <div class = "form-group">
                        <div class = "col-xs-offset-3 col-xs-9">
                            <button type = "submit" class = "btn btn-primary">Update Order</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>