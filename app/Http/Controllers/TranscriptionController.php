<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Str;

class TranscriptionController extends BaseController
{
    public function form()
    {
        return view('transcribe.form');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'audio_url' => 'nullable|url',
            'audio_file' => 'nullable|file|mimes:mp3,wav,m4a,webm|max:10240',
        ]);

        $url = $request->input('audio_url');
        $filename = 'audio_' . Str::uuid() . '.mp3';
        $path = "{$filename}";

        // Get file contents from URL or uploaded file
        if ($request->hasFile('audio_file')) {
            // Handle file upload
            $fileContents = file_get_contents($request->file('audio_file')->getRealPath());
        } elseif ($request->filled('audio_url')) {
            // Use Laravel's HTTP client to fetch the file safely
            try {
                $response = Http::timeout(30)->get($url);

                if (! $response->successful()) {
                    return back()->withErrors([
                        'audio_url' => 'Failed to download file. HTTP status: ' . $response->status()
                    ]);
                }

                $fileContents = $response->body();
            } catch (\Exception $e) {
                return back()->withErrors([
                    'audio_url' => 'Error downloading audio: ' . $e->getMessage()
                ]);
            }
        }

        // Save into storage/app/private/
        Storage::put($path, $fileContents);

        // Build full path for Whisper API
        $localPath = storage_path("app/private/{$path}");

        // Send to OpenAI Whisper
        $openaiResponse = Http::withToken(env('OPENAI_API_KEY'))
            ->attach(
                'file',
                file_get_contents($localPath),
                $filename
            )
            ->asMultipart()
            ->post('https://api.openai.com/v1/audio/transcriptions', [
                ['name' => 'model', 'contents' => 'whisper-1'],
            ]);

        $data = $openaiResponse->json();

        $transcript = $data['text'] ?? null;

        if (! $transcript) {
            return back()->withErrors(['audio_file' => 'Unable to transcribe the audio.']);
        }

        // Generate summary from transcript
        $summaryResponse = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that summarizes audio transcripts.'],
                    ['role' => 'user', 'content' => "Please summarize the following transcript:\n\n{$transcript}"]
                ],
                'temperature' => 0.7,
                'max_tokens' => 300,
            ]);

        $summary = $summaryResponse->json('choices.0.message.content') ?? 'Unable to summarize.';

        return view('transcribe.result', [
            'transcript' => $transcript,
            'summary' => $summary,
            'file' => $filename
        ]);
    }
}
