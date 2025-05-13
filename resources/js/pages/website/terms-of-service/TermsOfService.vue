<template>
    <div class="bg-gray-50">
        <!-- Hero Section -->
        <HeroSection title="Telcoflo Terms of Service" />

        <!-- Main Content -->
        <section class="py-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Introduction -->
                <div class="mb-10">
                    <p class="text-gray-700 leading-relaxed">
                        Welcome to Telcoflo! These <span class="font-semibold">Terms of Service ("ToS")</span> govern your access to and use of the Telcoflo platform and related services (collectively, the <span class="font-semibold">"Services"</span>) provided by <span class="font-semibold">Optimum Quality Proprietary Limited</span>, trading as Telcoflo ("Telcoflo", "we", "us", or "our"). We are a company incorporated in Botswana under the <span class="font-semibold">Companies Act (Cap. 42:01)</span> with registration number <span class="font-semibold">BW00001738923</span>, located at <span class="font-semibold">Plot 28562, Fair Grounds Mall, Samora Machel Drive, Gaborone, Botswana</span>.
                    </p>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Telcoflo is a <span class="font-semibold">Communications Platform as a Service (CPaaS)</span> provider that enables businesses to manage customer interactions across multiple channels, including chats, calls, and emails, through a centralized, AI-powered platform. Our Services facilitate automation, integrations with third-party tools, and streamlined workflows to enhance customer support and operational efficiency. By accessing or using our Services, you ("Customer", "you", or "your") agree to be bound by this ToS, our <router-link :to="{ name: 'privacy-policy' }" class="text-indigo-600 hover:underline">Privacy Policy</router-link>, and any additional terms referenced herein. If you do not agree to this ToS, you must not use our Services.
                    </p>
                </div>

                <!-- 1. Definitions -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Definitions</h2>
                    <div class="ml-4">
                        <!-- 1.1 Customer Data -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">1.1 Customer Data</h3>
                        <p class="text-gray-700 leading-relaxed">
                            <span class="font-semibold">"Customer Data"</span> means any data, including personal data, that you or your end-users provide or process through the Services, such as communication logs, messages, or contact information.
                        </p>

                        <!-- 1.2 End-User -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">1.2 End-User</h3>
                        <p class="text-gray-700 leading-relaxed">
                            <span class="font-semibold">"End-User"</span> means any individual or entity that interacts with the Services through your account, such as your customers, agents, or team members.
                        </p>

                        <!-- 1.3 Personal Data -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">1.3 Personal Data</h3>
                        <p class="text-gray-700 leading-relaxed">
                            <span class="font-semibold">"Personal Data"</span> has the meaning given in the <span class="font-semibold">Botswana Data Protection Act, 2018</span>, and includes any information relating to an identified or identifiable individual.
                        </p>

                        <!-- 1.4 Service Fees -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">1.4 Service Fees</h3>
                        <p class="text-gray-700 leading-relaxed">
                            <span class="font-semibold">"Service Fees"</span> means the fees payable by you for the use of the Services, as outlined in your subscription plan or as agreed in writing.
                        </p>

                        <!-- 1.5 Third-Party Services -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">1.5 Third-Party Services</h3>
                        <p class="text-gray-700 leading-relaxed">
                            <span class="font-semibold">"Third-Party Services"</span> means services, tools, or integrations provided by third parties, such as communication APIs, CRM systems, or AI tools, which may be used in conjunction with our Services.
                        </p>
                    </div>
                </div>

                <!-- 2. Use of the Services -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Use of the Services</h2>
                    <div class="ml-4">
                        <!-- 2.1 Eligibility -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">2.1 Eligibility</h3>
                        <p class="text-gray-700 leading-relaxed">
                            You must be a legally registered entity or individual authorized to conduct business to use our Services. By using the Services, you represent that you have the legal authority to enter into this agreement.
                        </p>

                        <!-- 2.2 License to Use -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">2.2 License to Use</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We grant you a non-exclusive, non-transferable, revocable license to access and use the Services for your internal business purposes, subject to this ToS. This license does not permit you to sublicense, resell, or distribute the Services without our prior written consent.
                        </p>

                        <!-- 2.3 Account Registration -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">2.3 Account Registration</h3>
                        <p class="text-gray-700 leading-relaxed">
                            To use the Services, you must register an account with us and provide accurate information, including your company details and contact information. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.
                        </p>

                        <!-- 2.4 Acceptable Use -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">2.4 Acceptable Use</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            You agree to use the Services in compliance with all applicable laws, including the <span class="font-semibold">Botswana Data Protection Act, 2018</span>, and international privacy laws such as the <span class="font-semibold">GDPR</span> (for EU users) and <span class="font-semibold">CCPA</span> (for California users). You must not:
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>Use the Services for any unlawful, fraudulent, or harmful purpose.</li>
                            <li>Interfere with or disrupt the integrity or performance of the Services.</li>
                            <li>Attempt to gain unauthorized access to the Services or related systems.</li>
                            <li>Use the Services to transmit malicious code, spam, or unsolicited communications.</li>
                            <li>Process sensitive personal data (as defined in the <span class="font-semibold">Botswana Data Protection Act, 2018</span>) without lawful grounds or appropriate safeguards.</li>
                        </ul>

                        <!-- 2.5 Customer Responsibilities -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">2.5 Customer Responsibilities</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            You are responsible for:
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>Ensuring that your use of the Services complies with all applicable laws and regulations.</li>
                            <li>Obtaining any necessary consents from End-Users before processing their personal data through the Services.</li>
                            <li>Configuring the Services (e.g., automation workflows, communication settings) in accordance with your legal obligations.</li>
                            <li>Maintaining the security of your systems and networks that interact with the Services.</li>
                        </ul>
                    </div>
                </div>

                <!-- 3. Service Features and Integrations -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Service Features and Integrations</h2>
                    <div class="ml-4">
                        <!-- 3.1 Core Features -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">3.1 Core Features</h3>
                        <p class="text-gray-700 leading-relaxed">
                            The Services provide a centralized platform for managing customer interactions across multiple channels, including but not limited to voice calls, chats, and emails. We offer features such as <span class="font-semibold">AI-powered automation</span>, workflow management, analytics, reporting, and dashboards to enhance customer support, team collaboration, and operational efficiency. Specific features available to you depend on your subscription plan.
                        </p>

                        <!-- 3.2 Third-Party Integrations -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">3.2 Third-Party Integrations</h3>
                        <p class="text-gray-700 leading-relaxed">
                            The Services support integrations with a wide range of Third-Party Services, including communication APIs, CRM systems, and AI tools, to facilitate seamless workflows and automation. You are responsible for complying with the terms of these Third-Party Services. We are not liable for any issues arising from your use of Third-Party Services, including downtime, data loss, or changes to their terms.
                        </p>

                        <!-- 3.3 Availability -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">3.3 Availability</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We strive to ensure the Services are available <span class="font-semibold">24/7/365</span>, subject to scheduled maintenance and unforeseen circumstances. We will notify you of any planned downtime in advance. However, we do not guarantee uninterrupted access and are not liable for any disruptions beyond our control.
                        </p>
                    </div>
                </div>

                <!-- 4. Customer Data and Privacy -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Customer Data and Privacy</h2>
                    <div class="ml-4">
                        <!-- 4.1 Ownership and Processing -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">4.1 Ownership and Processing</h3>
                        <p class="text-gray-700 leading-relaxed">
                            You retain ownership of all Customer Data. By using the Services, you grant us a worldwide, non-exclusive license to process Customer Data as necessary to provide the Services, including sharing with Third-Party Services to facilitate communication, automation, or integrations. We act as a <span class="font-semibold">data processor</span> on your behalf, and you are the <span class="font-semibold">data controller</span>, as defined under the <span class="font-semibold">Botswana Data Protection Act, 2018</span>.
                        </p>

                        <!-- 4.2 Data Protection -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">4.2 Data Protection</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We will process Customer Data in accordance with our <router-link :to="{ name: 'privacy-policy' }" class="text-indigo-600 hover:underline">Privacy Policy</router-link> and applicable laws, including the <span class="font-semibold">Botswana Data Protection Act, 2018</span>. You must ensure that your use of the Services, including the processing of End-User personal data, complies with all applicable privacy laws. You are responsible for notifying End-Users of how their data will be used, as required by law.
                        </p>

                        <!-- 4.3 Recordings -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">4.3 Recordings</h3>
                        <p class="text-gray-700 leading-relaxed">
                            If you enable recording of communications (e.g., voice calls, chats), you are responsible for obtaining consent from End-Users where required by law. Recordings are stored securely and retained as per your configuration settings or legal requirements.
                        </p>

                        <!-- 4.4 Data Security -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">4.4 Data Security</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We implement industry-standard security measures to protect Customer Data, including encryption, access controls, and secure hosting on cloud infrastructure. However, you acknowledge that no system is completely secure, and you are responsible for securing your own systems.
                        </p>

                        <!-- 4.5 Data Retention and Deletion -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">4.5 Data Retention and Deletion</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We retain Customer Data for as long as necessary to provide the Services or as required by law. You may request deletion of Customer Data in accordance with our <router-link :to="{ name: 'privacy-policy' }" class="text-indigo-600 hover:underline">Privacy Policy</router-link>, subject to legal retention obligations (see our <router-link :to="{ name: 'data-deletion-instructions' }" class="text-indigo-600 hover:underline">Data Deletion Instructions</router-link> for details).
                        </p>
                    </div>
                </div>

                <!-- 5. Payment Terms -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Payment Terms</h2>
                    <div class="ml-4">
                        <!-- 5.1 Service Fees -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">5.1 Service Fees</h3>
                        <p class="text-gray-700 leading-relaxed">
                            You agree to pay the Service Fees as outlined in your subscription plan or as agreed in writing. All fees are exclusive of taxes unless otherwise stated. As per the <span class="font-semibold">Exemption Letter from BURS dated 9th March 2021</span>, Telcoflo is not required to charge VAT unless our taxable supplies exceed <span class="font-semibold">P1,000,000 per annum</span>. However, you may be charged VAT by third-party providers.
                        </p>

                        <!-- 5.2 Invoicing and Payment -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">5.2 Invoicing and Payment</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We will invoice you based on your subscription plan (e.g., per user seat or usage-based). Payment is due within <span class="font-semibold">30 days</span> of the invoice date unless otherwise agreed. Late payments may incur interest at a rate of <span class="font-semibold">1.5% per month</span> or the maximum rate permitted by law.
                        </p>

                        <!-- 5.3 Fee Changes -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">5.3 Fee Changes</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We may adjust Service Fees upon <span class="font-semibold">30 days’ written notice</span>. If you do not agree to the revised fees, you may terminate your account as per <span class="font-semibold">Section 9</span>.
                        </p>
                    </div>
                </div>

                <!-- 6. Intellectual Property -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Intellectual Property</h2>
                    <div class="ml-4">
                        <!-- 6.1 Telcoflo’s Intellectual Property -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">6.1 Telcoflo’s Intellectual Property</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We retain all rights, title, and interest in the Services, including all software, AI tools, technology, and intellectual property. You are granted only the license to use the Services as outlined in <span class="font-semibold">Section 2.2</span>.
                        </p>

                        <!-- 6.2 Feedback -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">6.2 Feedback</h3>
                        <p class="text-gray-700 leading-relaxed">
                            If you provide feedback, suggestions, or improvements regarding the Services, we may use such feedback without restriction or compensation to you.
                        </p>
                    </div>
                </div>

                <!-- 7. Confidentiality -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Confidentiality</h2>
                    <div class="ml-4">
                        <!-- 7.1 Definition -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">7.1 Definition</h3>
                        <p class="text-gray-700 leading-relaxed">
                            <span class="font-semibold">"Confidential Information"</span> means any non-public information disclosed by one party to the other, including Customer Data, pricing, and technical details of the Services.
                        </p>

                        <!-- 7.2 Obligations -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">7.2 Obligations</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Each party agrees to protect the other’s Confidential Information with the same degree of care it uses to protect its own confidential information (but not less than reasonable care) and to use such information only to fulfill its obligations under this ToS. Confidential Information may be disclosed if required by law, provided the disclosing party notifies the other party promptly.
                        </p>
                    </div>
                </div>

                <!-- 8. Warranties and Disclaimers -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Warranties and Disclaimers</h2>
                    <div class="ml-4">
                        <!-- 8.1 Warranties -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">8.1 Warranties</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We warrant that the Services will perform substantially in accordance with their documentation under normal use. Your sole remedy for a breach of this warranty is termination of your account as per <span class="font-semibold">Section 9</span>.
                        </p>

                        <!-- 8.2 Disclaimers -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">8.2 Disclaimers</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Except as expressly stated, the Services are provided <span class="font-semibold">"as is"</span> without warranties of any kind, whether express or implied, including warranties of merchantability, fitness for a particular purpose, or non-infringement. We do not warrant that the Services will be error-free or uninterrupted, or that they will meet your specific requirements.
                        </p>
                    </div>
                </div>

                <!-- 9. Termination -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Termination</h2>
                    <div class="ml-4">
                        <!-- 9.1 Term -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">9.1 Term</h3>
                        <p class="text-gray-700 leading-relaxed">
                            This ToS takes effect upon your acceptance and continues until terminated by either party.
                        </p>

                        <!-- 9.2 Termination for Convenience -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">9.2 Termination for Convenience</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Either party may terminate this agreement with <span class="font-semibold">30 days’ written notice</span>.
                        </p>

                        <!-- 9.3 Termination for Cause -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">9.3 Termination for Cause</h3>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            We may terminate or suspend your account immediately if you:
                        </p>
                        <ul class="list-disc pl-6 text-gray-700 space-y-2">
                            <li>Breach this ToS, including non-payment of Service Fees.</li>
                            <li>Use the Services in a manner that violates applicable laws or causes harm to us or third parties.</li>
                        </ul>
                        <p class="text-gray-700 leading-relaxed mt-4">
                            You may terminate immediately if we materially breach this ToS and fail to cure the breach within <span class="font-semibold">30 days</span> of notice.
                        </p>

                        <!-- 9.4 Effect of Termination -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">9.4 Effect of Termination</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Upon termination, you must cease using the Services, and we will delete your Customer Data within <span class="font-semibold">30 days</span>, subject to legal retention requirements. Any outstanding Service Fees remain payable.
                        </p>
                    </div>
                </div>

                <!-- 10. Limitation of Liability -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Limitation of Liability</h2>
                    <div class="ml-4">
                        <!-- 10.1 Exclusion of Damages -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">10.1 Exclusion of Damages</h3>
                        <p class="text-gray-700 leading-relaxed">
                            To the maximum extent permitted by law, neither party will be liable for any indirect, incidental, consequential, or punitive damages, including loss of profits, data, or goodwill, arising out of or relating to this ToS or the Services.
                        </p>

                        <!-- 10.2 Liability Cap -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">10.2 Liability Cap</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Our total liability under this ToS will not exceed the Service Fees paid by you in the <span class="font-semibold">12 months</span> preceding the claim.
                        </p>

                        <!-- 10.3 Exceptions -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">10.3 Exceptions</h3>
                        <p class="text-gray-700 leading-relaxed">
                            The limitations in this section do not apply to liability for gross negligence, willful misconduct, or breaches of confidentiality or data protection obligations.
                        </p>
                    </div>
                </div>

                <!-- 11. Indemnification -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Indemnification</h2>
                    <div class="ml-4">
                        <!-- 11.1 By Telcoflo -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">11.1 By Telcoflo</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We will indemnify you against any third-party claim that the Services infringe their intellectual property rights, provided you notify us promptly and allow us to control the defense. Our liability under this section is subject to the liability cap in <span class="font-semibold">Section 10.2</span>.
                        </p>

                        <!-- 11.2 By Customer -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">11.2 By Customer</h3>
                        <p class="text-gray-700 leading-relaxed">
                            You will indemnify us against any claims arising from your use of the Services in violation of this ToS, including claims related to Customer Data or your failure to comply with applicable laws.
                        </p>
                    </div>
                </div>

                <!-- 12. Governing Law and Dispute Resolution -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">12. Governing Law and Dispute Resolution</h2>
                    <div class="ml-4">
                        <!-- 12.1 Governing Law -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">12.1 Governing Law</h3>
                        <p class="text-gray-700 leading-relaxed">
                            This ToS is governed by the laws of <span class="font-semibold">Botswana</span>. Any disputes will be subject to the exclusive jurisdiction of the courts in <span class="font-semibold">Gaborone, Botswana</span>.
                        </p>

                        <!-- 12.2 Dispute Resolution -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">12.2 Dispute Resolution</h3>
                        <p class="text-gray-700 leading-relaxed">
                            The parties will attempt to resolve disputes through good-faith negotiation. If unresolved within <span class="font-semibold">30 days</span>, either party may pursue legal action. Nothing in this section prevents a party from seeking injunctive relief to protect its rights.
                        </p>
                    </div>
                </div>

                <!-- 13. Compliance with Laws -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">13. Compliance with Laws</h2>
                    <div class="ml-4">
                        <!-- 13.1 General Compliance -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">13.1 General Compliance</h3>
                        <p class="text-gray-700 leading-relaxed">
                            You agree to comply with all applicable laws, including export control laws, anti-corruption laws, and data protection laws. You represent that you are not on any restricted party list or located in a country subject to sanctions that would prohibit your use of the Services.
                        </p>

                        <!-- 13.2 Data Protection -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">13.2 Data Protection</h3>
                        <p class="text-gray-700 leading-relaxed">
                            You must comply with the <span class="font-semibold">Botswana Data Protection Act, 2018</span>, and any other applicable privacy laws when using the Services. This includes obtaining consents for processing End-User personal data and notifying End-Users of data breaches as required by law.
                        </p>
                    </div>
                </div>

                <!-- 14. Miscellaneous -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">14. Miscellaneous</h2>
                    <div class="ml-4">
                        <!-- 14.1 Entire Agreement -->
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">14.1 Entire Agreement</h3>
                        <p class="text-gray-700 leading-relaxed">
                            This ToS, together with our <router-link :to="{ name: 'privacy-policy' }" class="text-indigo-600 hover:underline">Privacy Policy</router-link> and any written agreements, constitutes the entire agreement between the parties and supersedes all prior agreements.
                        </p>

                        <!-- 14.2 Amendments -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">14.2 Amendments</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We may update this ToS from time to time by posting the revised version on our website and notifying you. Continued use of the Services after such changes constitutes your acceptance of the updated ToS.
                        </p>

                        <!-- 14.3 Assignment -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">14.3 Assignment</h3>
                        <p class="text-gray-700 leading-relaxed">
                            You may not assign your rights or obligations under this ToS without our prior written consent. We may assign our rights and obligations to a successor in the event of a merger, acquisition, or sale of assets.
                        </p>

                        <!-- 14.4 Force Majeure -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">14.4 Force Majeure</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Neither party will be liable for delays or failures to perform due to events beyond their reasonable control, such as natural disasters, wars, or internet outages.
                        </p>

                        <!-- 14.5 Notices -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">14.5 Notices</h3>
                        <p class="text-gray-700 leading-relaxed">
                            Notices must be in writing and sent to the contact details provided (for you, as per your account information; for us, to the address listed above or email: <a href="mailto:legal@telcoflo.com" class="text-indigo-600 hover:underline">legal@telcoflo.com</a>).
                        </p>

                        <!-- 14.6 Severability -->
                        <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">14.6 Severability</h3>
                        <p class="text-gray-700 leading-relaxed">
                            If any provision of this ToS is found to be unenforceable, the remaining provisions will remain in full force and effect.
                        </p>
                    </div>
                </div>

                <!-- 15. Contact Us -->
                <div class="mb-10">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-4">15. Contact Us</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        For questions or concerns about this ToS, please contact us at:
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        <span class="font-semibold">Telcoflo (Pty) Ltd</span><br>
                        Plot 28562, Fair Grounds Mall, Samora Machel Drive<br>
                        Gaborone, Botswana<br>
                        Email: <a href="mailto:legal@telcoflo.com" class="text-indigo-600 hover:underline">legal@telcoflo.com</a><br>
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import HeroSection from '@Pages/website/components/HeroSection.vue';

    export default {
        name: 'TermsOfService',
        components: {
            HeroSection,
        },
    };
</script>
