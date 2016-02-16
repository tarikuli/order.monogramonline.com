<!doctype html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <title>Order by - {{$order->email}}</title>
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link type = "text/css" rel = "stylesheet"
          href = "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
</head>
<body>
    @include('includes.header_menu')
    <div class = "container">
        <ol class="breadcrumb">
            <li><a href="{{url('/')}}">Home</a></li>
            <li><a href="{{url('orders')}}">Orders</a></li>
            <li class="active">View order</li>
        </ol>
        <div class = "col-xs-offset-1 col-xs-10 col-xs-offset-1">
            <h4 class = "page-header">Order details</h4>
            <table class = "table table-hover table-bordered">
                <tr class = "success">
                    <td>Order ID</td>
                    <td>{{$order->order_id}}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{$order->email}}</td>
                </tr>
                <tr class = "success">
                    <td>Customer ID</td>
                    <td>{{$order->customer_id}}</td>
                </tr>
                <tr>
                    <td>Placed By</td>
                    <td>{{$order->placed_by}}</td>
                </tr>
                <tr class = "success">
                    <td>Store ID</td>
                    <td>{{$order->store_id}}</td>
                </tr>
                <tr>
                    <td>Market</td>
                    <td>{{$order->market}}</td>
                </tr>
                <tr class = "success">
                    <td>Order Date</td>
                    <td>{{$order->order_date}}</td>
                </tr>
                <tr>
                    <td>Paid</td>
                    <td>{{$order->paid}}</td>
                </tr>
                <tr class = "success">
                    <td>Payment Method</td>
                    <td>{{$order->payment_method}}</td>
                </tr>
                <tr>
                    <td>Sub Total</td>
                    <td>{{$order->sub_total}}</td>
                </tr>
                <tr class = "success">
                    <td>Shipping Cost</td>
                    <td>{{$order->shipping_cost}}</td>
                </tr>
                <tr>
                    <td>Discount</td>
                    <td>{{$order->discount}}</td>
                </tr>
                <tr class = "success">
                    <td>Gift Wrap Cost</td>
                    <td>{{$order->gift_wrap_cost}}</td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td>{{$order->tax}}</td>
                </tr>
                <tr class = "success">
                    <td>Adjustment</td>
                    <td>{{$order->adjustment}}</td>
                </tr>
                <tr>
                    <td>Order Total</td>
                    <td>{{$order->order_total}}</td>
                </tr>
                <tr class = "success">
                    <td>Fraud Score</td>
                    <td>{{$order->fraud_score}}</td>
                </tr>
                <tr>
                    <td>Coupon Name</td>
                    <td>{{$order->coupon_name}}</td>
                </tr>
                <tr class = "success">
                    <td>Shipping Method</td>
                    <td>{{$order->shipping_method}}</td>
                </tr>
                <tr>
                    <td>Four PL Unique ID</td>
                    <td>{{$order->four_pl_unique_id}}</td>
                </tr>
                <tr class = "success">
                    <td>Short Order</td>
                    <td>{{$order->short_order}}</td>
                </tr>
                <tr>
                    <td>Order Comments</td>
                    <td>{{$order->order_comments}}</td>
                </tr>
                <tr class = "success">
                    <td>Item Name</td>
                    <td>{{$order->item_name}}</td>
                </tr>
                <tr>
                    <td>Item Code</td>
                    <td>{{$order->item_code}}</td>
                </tr>
                <tr class = "success">
                    <td>Item ID</td>
                    <td>{{$order->item_id}}</td>
                </tr>
                <tr>
                    <td>Item QTY</td>
                    <td>{{$order->item_qty}}</td>
                </tr>
                <tr class = "success">
                    <td>Item Price</td>
                    <td>{{$order->item_price}}</td>
                </tr>
                <tr>
                    <td>Item Cost</td>
                    <td>{{$order->item_cost}}</td>
                </tr>
                <tr class = "success">
                    <td>Item Options</td>
                    <td>{!! nl2br($order->item_options) !!}</td>
                </tr>
                <tr>
                    <td>TRK</td>
                    <td>{{$order->trk}}</td>
                </tr>
                <tr class = "success">
                    <td>Ship Date</td>
                    <td>{{$order->ship_date}}</td>
                </tr>
                <tr>
                    <td>Shipping Carrier</td>
                    <td>{{$order->shipping_carrier}}</td>
                </tr>
                <tr class = "success">
                    <td>Drop Shipper</td>
                    <td>{{$order->drop_shipper}}</td>
                </tr>
                <tr>
                    <td>Return Request Code</td>
                    <td>{{$order->return_request_code}}</td>
                </tr>
                <tr class = "success">
                    <td>Return Request Date</td>
                    <td>{{$order->return_request_date}}</td>
                </tr>
                <tr>
                    <td>Return Disposition Code</td>
                    <td>{{$order->return_disposition_code}}</td>
                </tr>
                <tr class = "success">
                    <td>Return Date</td>
                    <td>{{$order->return_date}}</td>
                </tr>
                <tr>
                    <td>RMA</td>
                    <td>{{$order->rma}}</td>
                </tr>
                <tr class = "success">
                    <td>D S Purchase Order</td>
                    <td>{{$order->d_s_purchase_order}}</td>
                </tr>
                <tr>
                    <td>WF Batch</td>
                    <td>{{$order->wf_batch}}</td>
                </tr>
                <tr class = "success">
                    <td>Order Status</td>
                    <td>{{$order->order_status}}</td>
                </tr>
                <tr>
                    <td>Source</td>
                    <td>{{$order->source}}</td>
                </tr>
                <tr class = "success">
                    <td>Store ID</td>
                    <td>{{$order->cancel_code}}</td>
                </tr>
            </table>
        </div>
        <div class = "col-xs-12" style = "margin-bottom: 30px;">
            <div class = "col-xs-offset-1 col-xs-10" style="margin-bottom: 10px;">
                <a href = "{{ url(sprintf("/orders/%d/edit", $order->id)) }}" class = "btn btn-success btn-block">Edit this
                                                                                                        order</a>
            </div>
            <div class = "col-xs-offset-1 col-xs-10">
                {!! Form::open(['url' => url(sprintf('/orders/%d', $order->id)), 'method' => 'delete', 'id' => 'delete-order-form']) !!}
                {!! Form::submit('Delete this order', ['class'=> 'btn btn-danger btn-block', 'id' => 'delete-order-btn']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script type = "text/javascript" src = "//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script type = "text/javascript">
        var message = {
            delete: 'Are you sure you want to delete?',
        };
        $("input#delete-order-btn").on('click', function (event)
        {
            event.preventDefault();
            var action = confirm(message.delete);
            if ( action ) {
                var form = $("form#delete-order-form");
                form.submit();
            }
        });
    </script>
</body>
</html>