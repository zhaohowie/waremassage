<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div style="background:white; padding:20px; border-radius:8px;">
            <div id="calendar"></div>
        </div>
    </div>

    <script>
        window.calendarData = {
            date: @json($date),
            resources: @json($resources),
            events: @json($events),
            csrf: '{{ csrf_token() }}'
        };
    </script>
</x-app-layout>
