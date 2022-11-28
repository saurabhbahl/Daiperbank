@extends('layouts.app')

@section('content')
	<p>
		The file you requested wasn't quite ready yet.
	</p>

	<p>
		It is being generated now. It should be ready in 60 seconds.
	</p>

	<p>
		<small>
			If you see this page for more than a minute, please contact <a href="hsdb+support@jimsc.com">support</a>.
		</small>
	</p>
@stop

@section('js')
<script type"text/javascript">
(function() {
	setTimeout(function() {
		window.location.reload(true);
	}, 30 * 1000);
})();
</script>