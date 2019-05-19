<a href="/{{$routeName}}/{{ $id }}/edit" title="{{$editTitle}}" class="text-success mr-2"> 
  <i class="fa fa-pencil" aria-hidden="true"></i>
</a>

@if( ! @$hideDelete )
<form class="inline-form" method="POST" action="/{{$routeName}}/{{ $id }}" >
  @csrf
  @method('delete')
  <button type="submit" class="text-danger mr-2" title="{{$deleteTitle}}" style="display: inline-block; background: none; border: 0;">
    <i class="{{$deleteIcon}}" aria-hidden="true"></i>
  </button>
</form>
@endif