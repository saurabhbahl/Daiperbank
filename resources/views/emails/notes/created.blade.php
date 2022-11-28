@component('mail::message')
# New Comment Notification

<?= e($Note->Author->Agency->name); ?> has made a new comment on Order #<?= e($Model->full_id); ?>.

<? if ($Recipient->isAdmin()): ?>
@component('mail::button', ['url' => route('admin.order.view', [ $Model ])])
View Order
@endcomponent
<? else: ?>
@component('mail::button', ['url' => route('order.view', [ $Model ])])
View Order
@endcomponent
<? endif; ?>

<? if ( ! $Recipient->isAdmin()): ?>
If you have further questions regarding your order, please feel free to contact us.
<br><br>

Thank you for your continued support,
<br>
Healthy Steps Diaper Bank
<? endif; ?>
@endcomponent
