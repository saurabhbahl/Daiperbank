@component('mail::message')
# New Message from Healthy Steps Diaper Bank

You've received a new message from Healthy Steps:
<br>

<?= implode("\n", array_map(function($line) {
	return "> " . e($line);
}, explode("\n", $message))); ?>

@component('mail::button', ['url' => route('notifications.index')])
View Message
@endcomponent

If you have further questions regarding this message, please feel free to contact us.
<br><br>

Thank you for your continued support,
<br>
Healthy Steps Diaper Bank
@endcomponent
