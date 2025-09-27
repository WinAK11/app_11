<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Placed</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f6f6f6; margin: 0; padding: 0;">
    <table width="100%" bgcolor="#f6f6f6" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="600" align="center" bgcolor="#ffffff" cellpadding="30" cellspacing="0" style="margin: 40px auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <tr>
                        <td>
                            <h2 style="color: #2196F3; text-align: center; margin-bottom: 10px;">🎉 Thank you for your order! 🎉</h2>
                            <p style="font-size: 16px; color: #333; text-align: center;">Order Details:</p>
                            <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
                                <thead>
                                    <tr style="background: #f0f0f0;">
                                        <th align="left" style="border-bottom: 1px solid #ddd;">Product</th>
                                        <th align="center" style="border-bottom: 1px solid #ddd;">Quantity</th>
                                        <th align="right" style="border-bottom: 1px solid #ddd;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td align="center">{{ $item->qty }}</td>
                                        <td align="right">{{ number_format($item->subtotal(), 0, ',', ',') }}đ</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p style="font-size: 16px; color: #333; text-align: right;"><strong>Total: {{ number_format($order['total'], 0, ',', ',') }}đ</strong></p>
                            <div style="margin: 30px 0;">
                                <p style="font-size: 16px; color: #333;">Your order will be delivered to:</p>
                                <p style="font-size: 15px; color: #555;">
                                    {{ $order['name'] }},<br>
                                    {{ $order['phone'] }},<br>
                                    {{ $order['address'] }}, {{ $order['district'] }}, {{ $order['country'] }}<br>
                                    {{-- {{ $order['zip'] }} --}}
                                </p>
                            </div>
                            <p style="font-size: 15px; color: #888; text-align: center; margin-top: 30px;">Thank you for shopping with us.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>