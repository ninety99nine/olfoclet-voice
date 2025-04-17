<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transcript Result</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-8 space-y-6">
        <div class="flex justify-between items-center border-b pb-4">
            <h1 class="text-3xl font-bold text-gray-800">Audio Transcript</h1>
            <a href="{{ route('transcribe.form') }}"
               class="text-sm text-blue-600 hover:underline transition duration-150 ease-in-out">← Transcribe another</a>
        </div>

        <div class="text-sm text-gray-500">
            <span class="font-medium text-gray-700">File:</span> {{ $file }}
        </div>

        <div>
            <h2 class="text-xl font-semibold text-blue-700 mb-2">AI Summary</h2>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 text-gray-800 leading-relaxed shadow-sm">{{ $summary }}</div>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Full Transcript</h2>
            <div class="bg-gray-50 border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                <textarea readonly rows="16"
                          class="w-full p-4 text-sm text-gray-800 bg-transparent resize-none focus:outline-none">{{ $transcript }}</textarea>
            </div>
        </div>
    </div>
</body>
</html>
