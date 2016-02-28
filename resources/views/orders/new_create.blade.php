<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>Create order</title>
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
    @include('includes.header_menu')
    <div class = "container">
        <ol class="breadcrumb">
            <li><a href="{{url('/')}}">Home</a></li>
            <li><a href="{{url('orders')}}">Orders</a></li>
            <li class="active">Create order</li>
        </ol>
        @if($errors->any())
            <div class = "col-xs-12">
                <div class = "alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {!! Form::open(['url' => url('orders'), 'method' => 'post', 'class'=>'form-horizontal', 'role'=>'form']) !!}
        <div class = "form-group">
            {!!Form::label('order_id','Order id:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('order_id', null, ['id' => 'order_id','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('short_order','Short Order:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('short_order', null, ['id' => 'short_order','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('item_count','Item Count:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('item_count', null, ['id' => 'item_count','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('coupon_description','Coupon Description:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('coupon_description', null, ['id' => 'coupon_description','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('coupon_id','Coupon Id:',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('coupon_id', null, ['id' => 'coupon_id','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('coupon_value','Coupon Value: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('coupon_value', null, ['id' => 'coupon_value','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('shipping_charge','Shipping Charge: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('shipping_charge', null, ['id' => 'shipping_charge','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('tax_charge','Tax Charge: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('tax_charge', null, ['id' => 'tax_charge','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('total','Total: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('total', null, ['id' => 'total','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('card_name','Card Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('card_name', null, ['id' => 'card_name','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('card_expiry','Card Expiry: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('card_expiry', null, ['id' => 'card_expiry','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('order_comments','Order Comments: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('order_comments', null, ['id' => 'order_comments','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('order_date','Order Date: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('order_date', null, ['id' => 'order_date','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('order_ip','Order Ip: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('order_ip', null, ['id' => 'order_ip','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('paypal_merchant_email','Paypal Merchant Email: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('paypal_merchant_email', null, ['id' => 'paypal_merchant_email','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('paypal_txid','Paypal Txid: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('paypal_txid', null, ['id' => 'paypal_txid','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('space_id','Space Id: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('space_id', null, ['id' => 'space_id','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('store_id','Store Id: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('store_id', null, ['id' => 'store_id','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('store_name','Store Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('store_name', null, ['id' => 'store_name','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_state','Ship State: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_state', null, ['id' => 'ship_state','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('order_status','Order Status: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('order_status', null, ['id' => 'order_status','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('sub_total','Sub Total: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('sub_total', null, ['id' => 'sub_total','class'=>'form-control']) !!}
            </div>
        </div>

        <div class = "form-group">
            Customer Information
        </div>

        {{-------order---------}}



        <div class = "form-group">
            {!!Form::label('ship_full_name','Ship Full Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_full_name', null, ['id' => 'ship_full_name','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_first_name','Ship First Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_first_name', null, ['id' => 'ship_first_name','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_last_name','Ship Last Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_last_name', null, ['id' => 'ship_last_name','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_company_name','Ship Company Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_company_name', null, ['id' => 'ship_company_name','class'=>'form-control', 'rows' => 4]) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_address_1','Ship Address 1: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_address_1', null, ['id' => 'ship_address_1','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_address_2','Ship Address 2: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_address_2', null, ['id' => 'ship_address_2','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_city','Ship City: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_city', null, ['id' => 'ship_city','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_state','Ship State: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_state', null, ['id' => 'ship_state','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_zip','Ship Zip: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_zip', null, ['id' => 'ship_zip','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_country','Ship Country: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_country', null, ['id' => 'ship_country','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_phone','Ship Phone: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_phone', null, ['id' => 'ship_phone','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('ship_email','Ship Email: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('ship_email', null, ['id' => 'ship_email','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('shipping','Shipping: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('shipping', null, ['id' => 'shipping','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_full_name','Bill Full Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_full_name', null, ['id' => 'bill_full_name','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_first_name','Bill First Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_first_name', null, ['id' => 'bill_first_name','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_last_name','Bill Last Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_last_name', null, ['id' => 'bill_last_name','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_company_name','Bill Company Name: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_company_name', null, ['id' => 'bill_company_name','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_address_1','Bill Address 1: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_address_1', null, ['id' => 'bill_address_1','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_address_2','Bill Address 2: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_address_2', null, ['id' => 'bill_address_2','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_city','Bill City: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_city', null, ['id' => 'bill_city','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_state','Bill State: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_state', null, ['id' => 'bill_state','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_zip','Bill Zip: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_zip', null, ['id' => 'bill_zip','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_country','Bill Country: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_country', null, ['id' => 'bill_country','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_phone','Bill Phone: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_phone', null, ['id' => 'bill_phone','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_email','Bill Email: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_email', null, ['id' => 'bill_email','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            {!!Form::label('bill_mailing_list','Bill Mailing List: ',['class'=>'control-label col-xs-offset-2 col-xs-2'])!!}
            <div class = "col-xs-5">
                {!! Form::text('bill_mailing_list', null, ['id' => 'bill_mailing_list','class'=>'form-control']) !!}
            </div>
        </div>
        <div class = "form-group">
            <div class = "col-xs-offset-4 col-xs-5">
                {!! Form::submit('Create order', ['class'=>'btn btn-primary btn-block']) !!}
            </div>
        </div>
    </div>

    {!! Form::close() !!}
    </div>
</body>
</html>