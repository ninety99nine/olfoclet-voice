<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TranscriptionController;
use App\Models\KnowledgeBase;
use App\Models\Article;
use App\Models\WebsitePage;
use App\Services\FirecrawlService;
use App\Services\OpenAiService;
use App\Services\PineconeService;
use Ramsey\Uuid\Uuid;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/send-email', function () {

    $user = \App\Models\User::find('0196bf95-7f71-72b2-8b2b-bd7255a44eae');

    $user->update(['password' => null]);

    $token = \Illuminate\Support\Str::random(60);

    \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $user->email],
        ['token' => hash('sha256', $token), 'created_at' => now()]
    );

    $setupUrl = config('app.frontend_url') . '/setup-account?token=' . $token . '&email=' . urlencode($user->email);
    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\UserAccountCreated($user->email, $setupUrl));

    return 'Email send!';
});

// Route to create a knowledge base and articles for testing
Route::get('/test-create-knowledge-base', function () {
    try {
        // Create the knowledge base for Orange
        $knowledgeBase = new KnowledgeBase();
        $knowledgeBase->id = Uuid::uuid4()->toString();
        $knowledgeBase->name = 'Orange Support';
        $knowledgeBase->organization_id = '0196672a-edd2-717f-a26c-565300b4392c';
        $knowledgeBase->save();

        // Create Article 1: How to Reset a SIM Card
        $article1 = new Article();
        $article1->id = Uuid::uuid4()->toString();
        $article1->title = 'How to Reset a SIM Card on Orange Botswana';
        $article1->content = 'To reset your SIM card on Orange Botswana, dial *123# from your mobile device and follow the prompts to reset your SIM. Ensure your device is powered on and has a signal. If the issue persists, visit your nearest Orange service center or contact support at 123.';
        $article1->ai_searchable = true;
        $article1->organization_id = '0196672a-edd2-717f-a26c-565300b4392c';
        $article1->knowledge_base_id = $knowledgeBase->id;
        $article1->save();

        // Create Article 2: Checking Account Balance
        $article2 = new Article();
        $article2->id = Uuid::uuid4()->toString();
        $article2->title = 'How to Check Your Account Balance on Orange Botswana';
        $article2->content = 'To check your account balance, dial *124# and press the call button. You’ll receive an SMS with your current balance and data usage details. Alternatively, you can log into the Orange Botswana app to view your balance and manage your account.';
        $article2->ai_searchable = true;
        $article2->organization_id = '0196672a-edd2-717f-a26c-565300b4392c';
        $article2->knowledge_base_id = $knowledgeBase->id;
        $article2->save();

        // Create Article 3: Troubleshooting Network Issues
        $article3 = new Article();
        $article3->id = Uuid::uuid4()->toString();
        $article3->title = 'Troubleshooting Network Issues with Orange Botswana';
        $article3->content = 'If you’re experiencing network issues, first ensure your device is in an area with coverage. Restart your phone to refresh the connection. If the problem persists, dial *125# to run a network diagnostic, or contact Orange support at 123 for assistance. You can also check for outages on our website.';
        $article3->ai_searchable = true;
        $article3->organization_id = '0196672a-edd2-717f-a26c-565300b4392c';
        $article3->knowledge_base_id = $knowledgeBase->id;
        $article3->save();

        // Embed and upsert the articles into Pinecone with unique tags
        $openAiService = new OpenAiService();
        $pineconeService = new PineconeService();

        // Article 1 metadata with tags
        $embedding1 = $openAiService->generateEmbedding($article1->content);
        $metadata1 = [
            'knowledge_base_id' => $knowledgeBase->id,
            'type' => 'article',
            'content' => $article1->content,
            'tags' => ['123']
        ];
        $pineconeService->upsertVector($article1->id, $embedding1, $article1->organization_id, $metadata1);

        // Article 2 metadata with tags
        $embedding2 = $openAiService->generateEmbedding($article2->content);
        $metadata2 = [
            'knowledge_base_id' => $knowledgeBase->id,
            'type' => 'article',
            'content' => $article2->content,
            'tags' => ['124']
        ];
        $pineconeService->upsertVector($article2->id, $embedding2, $article2->organization_id, $metadata2);

        // Article 3 metadata with tags
        $embedding3 = $openAiService->generateEmbedding($article3->content);
        $metadata3 = [
            'knowledge_base_id' => $knowledgeBase->id,
            'type' => 'article',
            'content' => $article3->content,
            'tags' => ['125']
        ];
        $pineconeService->upsertVector($article3->id, $embedding3, $article3->organization_id, $metadata3);

        return response()->json([
            'message' => 'Knowledge base and articles created successfully.',
            'knowledge_base_id' => $knowledgeBase->id,
            'article_ids' => [$article1->id, $article2->id, $article3->id],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to create knowledge base and articles: ' . $e->getMessage(),
        ], 500);
    }
});

// Route to test RAG with a hardcoded user prompt
Route::get('/rag-article', function () {
    try {
        $openAiService = new OpenAiService();
        $pineconeService = new PineconeService();

        $organizationId = '0196672a-edd2-717f-a26c-565300b4392c'; // Orange organization ID
        $userPrompt = 'How do I reset my SIM card on Orange Botswana?';

        // Step 1: Embed the user prompt
        $queryEmbedding = $openAiService->generateEmbedding($userPrompt);

        // Step 2: Retrieve relevant context from Pinecone
        $matches = $pineconeService->queryVectors($queryEmbedding, $organizationId, 3);

        // Step 3: Match relevant results to database entries
        $matchingArticleIds = collect($matches)->pluck('id')->all();
        $articles = Article::whereIn('id', $matchingArticleIds)->select('title', 'content')->get();

        $context = $articles->map(function($article) {
            return 'Title: '.$article->title."\n".'Content: '.$article->content;
        })->join("\n\n");

        // Step 4: Prompt OpenAI with the retrieved context
        $response = $openAiService->promptWithContext($userPrompt, $context);

        return response()->json([
            'prompt' => $userPrompt,
            'retrieved_context' => $matches,
            'response' => $response,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to test RAG: ' . $e->getMessage(),
        ], 500);
    }
});

Route::get('/crawl-website', function () {
    try {

        $url = request()->query('url'); // Get URL from query parameter, e.g., ?url=https://example.com

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid or missing URL parameter');
        }

        // Initialize services
        $firecrawlService = new FirecrawlService();
        $openAiService = new OpenAiService();
        $pineconeService = new PineconeService();

        // Fetch and process website content using Firecrawl
        $content = $firecrawlService->crawlWebsite($url);

        dd($content);

        // Create or retrieve the knowledge base for Orange
        $knowledgeBase = KnowledgeBase::first();

        // Create a new WebContent entry
        $webContent = new WebsitePage();
        $webContent->url = $url;
        $webContent->content = $content;
        $webContent->ai_searchable = true;
        $webContent->id = Uuid::uuid4()->toString();
        $webContent->knowledge_base_id = $knowledgeBase->id;
        $webContent->organization_id = $knowledgeBase->organization_id;
        $webContent->save();

        // Embed and upsert the content into Pinecone
        $embedding = $openAiService->generateEmbedding($content);
        $metadata = [
            'knowledge_base_id' => $knowledgeBase->id,
            'type' => 'web_content',
            'content' => $content,
            'url' => $url,
            'tags' => ['website_page', $webContent->url]
        ];
        $pineconeService->upsertVector($webContent->id, $embedding, $webContent->organization_id, $metadata);

        return response()->json([
            'message' => 'Website content indexed successfully.',
            'web_content_id' => $webContent->id,
            'knowledge_base_id' => $knowledgeBase->id,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to index website content: ' . $e->getMessage(),
        ], 500);
    }
});

Route::get('/transcribe', [TranscriptionController::class, 'form'])->name('transcribe.form');
Route::post('/transcribe', [TranscriptionController::class, 'submit'])->name('transcribe.submit');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
