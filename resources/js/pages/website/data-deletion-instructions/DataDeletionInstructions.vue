<template>
    <div class="bg-gray-50">

        <!-- Hero Section -->
        <HeroSection title="Telcoflo Data Deletion Instructions" />

        <!-- Main Content -->
        <section class="py-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Introduction -->
                <div class="mb-10">
                    <p class="text-gray-700 leading-relaxed">
                        At <span class="font-semibold">Telcoflo (Pty) Ltd</span> ("Telcoflo", "we", "us", or "our"), we are committed to ensuring that you can exercise your rights regarding your data, including the right to request deletion of your personal information or customer data processed through our Services. This document outlines the steps to request data deletion in accordance with applicable laws, including the <span class="font-semibold">Botswana Data Protection Act, 2018</span>, the <span class="font-semibold">General Data Protection Regulation (GDPR)</span>, and the <span class="font-semibold">California Consumer Privacy Act (CCPA)</span>.
                    </p>
                </div>

                <!-- Scope of Data Deletion -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Scope of Data Deletion</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        You may request the deletion of:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                        <li><span class="font-semibold">Personal information</span> we hold about you, such as your contact details or account information.</li>
                        <li><span class="font-semibold">Customer Data</span> you have provided or processed through our Services, such as communication logs, messages, or recordings.</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed">
                        We will delete your data unless we are required to retain it for legal reasons (e.g., tax or audit obligations) or as otherwise permitted by law.
                    </p>
                </div>

                <!-- How to Request Data Deletion -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">How to Request Data Deletion</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        To request the deletion of your data, please follow these steps:
                    </p>
                    <div class="ml-4">
                        <!-- Step 1: Submit a Request -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Step 1: Submit a Request</h3>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-4">
                            <li>Email us at <a href="mailto:privacy@telcoflo.com" class="text-indigo-600 hover:underline">privacy@telcoflo.com</a> with the subject line <span class="font-semibold">"Data Deletion Request."</span></li>
                        </ul>
                        <p class="text-gray-700 leading-relaxed mb-2">
                            In your email, include the following details:
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>Your full name and company name (if applicable).</li>
                            <li>The email address associated with your Telcoflo account.</li>
                            <li>A description of the data you wish to delete (e.g., account information, communication logs, recordings).</li>
                            <li>If applicable, specify the time period or specific records you want deleted.</li>
                        </ul>

                        <!-- Step 2: Verification -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Step 2: Verification</h3>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>We will verify your identity to ensure the request is legitimate. This may involve asking for additional information, such as your account ID or a government-issued ID (for individual requests).</li>
                            <li>If the request is made on behalf of another individual (e.g., an End-User), you must provide proof of authorization to act on their behalf.</li>
                        </ul>

                        <!-- Step 3: Processing Your Request -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Step 3: Processing Your Request</h3>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>We will acknowledge receipt of your request within <span class="font-semibold">5 business days</span>.</li>
                            <li>We will process your request within <span class="font-semibold">30 days</span>, as required by laws such as GDPR, unless an extension is necessary (e.g., for complex requests). We will notify you if an extension is required.</li>
                            <li>If we cannot fulfill your request due to legal obligations (e.g., retention for tax purposes), we will inform you of the reasons and the applicable retention period.</li>
                        </ul>

                        <!-- Step 4: Confirmation -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Step 4: Confirmation</h3>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>Once your data is deleted, we will confirm the deletion in writing via email.</li>
                            <li>Note that deletion is permanent and may affect your ability to use certain features of the Services (e.g., if account data is deleted, you will no longer be able to access your account).</li>
                        </ul>
                    </div>
                </div>

                <!-- Data Deletion Upon Account Termination -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Data Deletion Upon Account Termination</h2>
                    <p class="text-gray-700 leading-relaxed">
                        If you terminate your account with Telcoflo, as outlined in our <router-link :to="{ name: 'terms-of-service' }" class="text-indigo-600 hover:underline">Terms of Service</router-link>, we will automatically delete your Customer Data within <span class="font-semibold">30 days</span> of termination, subject to legal retention requirements. You do not need to submit a separate deletion request unless you wish to delete specific data prior to termination.
                    </p>
                </div>

                <!-- Exceptions to Deletion -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Exceptions to Deletion</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        We may retain certain data if required by law or for legitimate business purposes, such as:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 space-y-2">
                        <li><span class="font-semibold">Compliance with legal obligations</span> (e.g., tax or audit records).</li>
                        <li><span class="font-semibold">Protection of our legal rights</span> (e.g., to defend against claims).</li>
                        <li><span class="font-semibold">Aggregated or anonymized data</span> that does not identify individuals.</li>
                    </ul>
                </div>

                <!-- Contact Us -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Contact Us</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        If you have questions about the data deletion process or need assistance, please contact us at:
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        <span class="font-semibold">Telcoflo (Pty) Ltd</span><br>
                        Plot 28562, Fair Grounds Mall, Samora Machel Drive<br>
                        Gaborone, Botswana<br>
                        Email: <a href="mailto:privacy@telcoflo.com" class="text-indigo-600 hover:underline">privacy@telcoflo.com</a><br>
                    </p>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        For EU residents, our Data Protection Officer can be reached at <a href="mailto:dpo@telcoflo.com" class="text-indigo-600 hover:underline">dpo@telcoflo.com</a>.
                    </p>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        We are committed to assisting you with your data deletion requests in a timely and lawful manner.
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import HeroSection from '@Pages/website/components/HeroSection.vue';

    export default {
        name: 'DataDeletionInstructions',
        components: {
            HeroSection,
        },
    };
</script>
