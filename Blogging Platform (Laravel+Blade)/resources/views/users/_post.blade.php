<x-card>
  @if ($post->hasThumbnail())
    <x-slot:image>
      <a href="{{ route('posts.show', $post)}}">
        <img src="{{ $post->thumbnail->getUrl('thumb') }}" alt="{{ $post->thumbnail->name }}" class="card-img-top">
      </a>
    </x-slot>
  @endif

  <h4 class="card-title">
    <a href="{{ route('posts.show', $post) }}">
      {{ $post->title }}
    </a>
  </h4>

  <p class="card-text">
    <small class="text-body-secondary">@humanize_date($post->posted_at)</small><br>
  </p>
</x-card>
