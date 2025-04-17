<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transcribe Audio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-2xl">

        <img src="/images/logo.png" class="w-40 mb-4"/>

        <h1 class="text-2xl font-bold mb-4">Transcribe Audio</h1>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('transcribe.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Audio URL (optional)</label>
                <input type="url" name="audio_url" placeholder="https://example.com/audio.mp3" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Or Upload MP3 File</label>
                <input type="file" name="audio_file" accept="audio/*" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                Transcribe
            </button>
        </form>
    </div>
</body>
</html>
