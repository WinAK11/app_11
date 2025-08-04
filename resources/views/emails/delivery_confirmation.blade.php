<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Delivery Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f6f6f6; margin: 0; padding: 0;">
    <table width="100%" bgcolor="#f6f6f6" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="600" align="center" bgcolor="#ffffff" cellpadding="30" cellspacing="0" style="margin: 40px auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <tr>
                        <td align="center">
                            <h2 style="color: #4CAF50; margin-bottom: 10px;">🎉 Delivery Confirmation 🎉</h2>
                            <p style="font-size: 16px; color: #333;">Dear <strong>{{ $order->name }}</strong>,</p>
                            <p style="font-size: 16px; color: #333;">Your order <strong>#{{ $order->id }}</strong> has been <span style="color: #4CAF50;">delivered</span>!</p>
                            <p style="font-size: 15px; color: #555;">Delivered Date: <strong>{{ $order->delivered_date }}</strong></p>
                            <a href="{{ url('/account-order/'.$order->id.'/details') }}" style="display: inline-block; margin: 20px 0 10px 0; padding: 12px 28px; background: #4CAF50; color: #fff; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;">
                                View Order
                            </a>
                            <p style="font-size: 15px; color: #888; margin-top: 30px;">Thank you for shopping with us.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>