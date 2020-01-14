<tr>
    <td class="header">
        @if( @$customImg )
            <img style='padding:0 1rem; margin: auto; display:block;' src="<?php echo asset("images/$customImg"); ?>">
        @else
            <img src="{{ asset('assets/images/email/header.jpg') }}">
        @endif
    </td>
</tr>