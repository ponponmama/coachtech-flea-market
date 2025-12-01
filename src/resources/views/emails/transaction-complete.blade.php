<table width="100%" style="max-width: 1500px; margin: auto; border-spacing: 0;">
    <tr>
        <td style="text-align: center; padding: 20px;">
            <h1 style="font-size: 24px; color: black; font-weight: 700; margin-bottom: 2%;">取引が完了しました</h1>
            <p style="font-size: 20px; color: black; margin-bottom: 2%;">{{ $seller->name }} さん</p>
            <p style="font-size: 16px; font-weight: 400; color: black; margin-bottom: 2%;">
                商品「{{ $item->name }}」の取引が完了しました。
            </p>
            <p style="font-size: 16px; font-weight: 400; color: black; margin-bottom: 2%;">
                購入者：{{ $buyer->name }} さん
            </p>
            <a href="{{ route('transaction.chat', ['item_id' => $item->id]) }}"
                style="width:257px;line-height: 69px; background-color: #D9D9D9; color: black; border: 1px solid #D9D9D9; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 24px; display: inline-block; margin: 5%;">取引チャットを確認する</a>
            <p style="font-size: 14px; color: black;">取引チャット画面から購入者を評価することができます。</p>
            <p style="font-size: 14px; color: black;">よろしくお願いいたします。<br>{{ config('app.name') }}</p>
        </td>
    </tr>
</table>
