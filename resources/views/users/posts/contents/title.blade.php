<div class="card-header bg-white py-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <a href="{{route('profile.show', $post->user->id)}}">
                @if ($post->user->avatar)
                    <img src="{{asset('storage/avatars/' . $post->user->avatar)}}" alt="{{ $post->user->avatar }}" class="rounded-circle avatar-sm">
                @else
                    <i class="fa-solid fa-circle-user text-secondary"></i>
                @endif
            </a>
        </div>
        <div class="col ps-0">
            <a href="{{route('profile.show', $post->user->id)}}" class="text-decoration-none text-dark">{{ $post->user->name }}</a>
        </div>
        <div class="col-auto">
            <div class="dropdown">
                <button class="btn btn-sm shadow-none" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>

                {{-- Show the edit and delete button to the owner of the post, otherwise do not show it --}}
                @if (Auth::user()->id === $post->user->id)
                    <div class="dropdown-menu">
                        <a href="{{route('post.edit', $post->id)}}" class="dropdown-item">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>
                        <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#delete-post-{{$post->id}}">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </div>
                    @include('users.posts.contents.modals.delete')
                @else
                    {{-- If the user is not the owner of the post, Unfollow Button -- will be discussed later on --}}
                    <div class="dropdown-menu">
                        <form action="#" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">Unfollow</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
