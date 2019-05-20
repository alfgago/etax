<a href="/{{$routeName}}/{{ $id }}/edit" title="{{$editTitle}}" class="text-success mr-2"> 
  <i class="fa fa-pencil" aria-hidden="true"></i>
</a>

@if( ! @$hideDelete )
<form id="delete-form-{{ $id }}" class="inline-form" method="POST" action="/{{$routeName}}/{{ $id }}" >
  @csrf
  @method('delete')
  <a type="button" class="text-danger mr-2" title="{{$deleteTitle}}" style="display: inline-block; background: none; border: 0;" onclick="confirmDelete({{ $id }});">
    <i class="{{$deleteIcon}}" aria-hidden="true"></i>
  </a>
</form>
@endif
