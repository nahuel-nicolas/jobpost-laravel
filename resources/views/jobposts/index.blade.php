<x-layout>
  {{-- @if (!Auth::check())
    @include('partials._hero')
  @endif

  @include('partials._search') --}}

  <div class="lg:grid lg:grid-cols-2 gap-4 space-y-4 md:space-y-0 mx-4">

    @unless(count($jobposts) == 0)

    @foreach($jobposts as $jobpost)
    <x-jobpost-card :jobpost="$jobpost" />
    @endforeach

    @else
    <p>No jobposts found</p>
    @endunless

  </div>

  <div class="mt-6 p-4">
    {{$jobposts->links()}}
  </div>
</x-layout>
