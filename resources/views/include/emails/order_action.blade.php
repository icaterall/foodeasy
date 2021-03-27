
                <table border="0" style="border: 0;" width="100%">
                    <tbody>
                        <tr>
                            <td width="100%" align="center">
                                <table cellpadding="0" border="0" style="border: 0; margin: 0;" cellspacing="0" width="585">
                                    <tbody>
                                        <tr>
                                            <td align="left" style="text-align: left;">
                                                <p>
                                                    <img border="0" width="170" height="40" src="https://spoongate.com/csfiles/img/logo.png" class="CToWUd" />
                                                </p>

                                                <p
                                                    style="
                                                        border-bottom-color: #999;
                                                        border-bottom-style: dotted;
                                                        border-bottom-width: 1px;
                                                        color: #555555;
                                                        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
                                                        font-size: 20px;
                                                        font-weight: bold;
                                                        margin-bottom: 5px;
                                                        padding-bottom: 5px;
                                                    "
                                                >
                                                    SpoonGate :: <span style="color: #668d00;">{{$order_status}} </span> Order #{{ $order_id }} / {{$order->foods->first()->restaurant->name}}
                                                </p>

                                                <p
                                                    style="
                                                        border-bottom-color: #999;
                                                        border-bottom-style: dotted;
                                                        border-bottom-width: 1px;
                                                        color: #888888;
                                                        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
                                                        font-size: 14px;
                                                        line-height: 130%;
                                                        margin-top: 0px;
                                                        padding-bottom: 5px;
                                                    "
                                                >
                                                    {{$order->date}} at {{$order->time}} -- {{$order->deliveryAddress->address}}

                                                </p>

                                                <p style="color: #555555; font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif; font-size: 14px; line-height: 130%;">
                                                    <b>{{ $email_title }}</b>
                                                </p>

                                                <p style="color: #555555; font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif; font-size: 14px; line-height: 130%; padding-bottom: 15px; padding-top: 15px;">
                                                    <a style="
                                                           background: rgb(182, 6, 6);
                                                            border-radius: 4px;
                                                            border: 1px solid rgb(137,6,6);
                                                            color: rgb(255, 255, 255);
                                                            display: block;
                                                            font-family: Lato, Helvetica Neueg st, Arial, Helvetica, sans-serif;
                                                            font-weight: 700;
                                                            line-height: 1em;
                                                            margin: 0px auto;
                                                            max-width: 25%;
                                                            outline: 0px;
                                                            padding: 0.571429rem 1.14286rem;
                                                            text-align: center;
                                                            text-decoration: none;"
                                                        href="{{$order_url}}"
                                                        >
                                                        View Order #{{ $order_id }}
                                                    </a>
                                                </p>

                                                <div style="border-bottom-color: #999; border-bottom-style: dotted; border-bottom-width: 2px; margin-bottom: 20px; margin-top: 0px; padding-bottom: 20px;"></div>

                                                <div style="color: #000; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 10px;">
                                                    <div style="font-weight: bold; padding: 5px 0 2px;">
                                                        Order Number
                                                    </div>
                                                    <div style="margin-bottom: 10px;">
                                                        #{{ $order_id }}
                                                    </div>

                                                    <div style="font-weight: bold; padding: 5px 0 2px;">
                                                        Customer
                                                    </div>
                                                    <div style="margin-bottom: 10px;">
                                                        {{$order->user->name}}
                                                    </div>

                                                    <div style="font-weight: bold; padding: 5px 0 2px;">
                                                        Date &amp; Time
                                                    </div>

                                                    <div style="margin-bottom: 10px;">
                                                        {{$order->date}} at {{$order->time}}
                                                    </div>
                                                    <div style="font-weight: bold; padding: 5px 0 2px;">
                                                        Order Type
                                                    </div>

                                                    <div style="margin-bottom: 10px;">
                                                        
                                                     {{($order->isdelivery == 1)?"Delivery":'Pickup'}}   
                                                    
                                                    </div>

                                                    <div style="font-weight: bold; padding: 5px 0 2px;">
                                                        Address
                                                    </div>

                                                    <div style="margin-bottom: 10px;">
                                                    {{$order->deliveryAddress->address}}
                                                    </div>

                                                    <div style="border-top-color: #555; border-top-style: solid; border-top-width: 2px; font-weight: bold; margin-top: 10px;">
                                                        <div style="float: right;">MYR {{$total_payment}}</div>
                                                        Total
                                                    </div>
                                                </div>

                                                <p style="color: #555555; font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif; font-size: 14px; line-height: 130%;">
                                                    Link not working?
                                                    <br />
                                                    Copy and paste this URL into your browser:
                                                    <br />
                                                    <a href="{{$order_url}}">{{$order_url}}</a>
                                                </p>

                                                <hr style="margin-bottom: 24px;" />

                                                <h3 style="color: #565a5c; font-family: 'Lato', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif; font-size: 16px; font-weight: bold;">
                                                    Thank you for using SpoonGate! 
                                                    <br />
                                                    – The SpoonGate Team
                                                </h3>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>