@props(['jobpost'])
<x-card>
  <div class="flex">
    <?php syslog(1, 'my message'); ?>
    <img class="hidden w-48 mr-6 md:block"
      src="{{$jobpost->logo ? asset('storage/' . $jobpost->logo) : asset('/images/no-image.png')}}" alt="" />
    <div>
      <h3 class="text-2xl">
        <a href="/jobposts/{{$jobpost->id}}">{{$jobpost->title}}</a>
      </h3>
      <div class="text-xl font-bold mb-4">{{$jobpost->company}}</div>
      <x-jobpost-tags :tagsCsv="$jobpost->tags" />
      <div class="text-lg mt-4">
        <i class="fa-solid fa-location-dot"></i> {{$jobpost->location}}
      </div>
    </div>
  </div>
</x-card>