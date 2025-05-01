<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 p-6">

      <!-- Header Section -->
      <div class="max-w-2xl mx-auto text-center mt-4 mb-8">
        <h1 class="w-fit mx-auto text-3xl font-bold bg-gradient-to-r from-red-600 to-indigo-600 bg-clip-text text-transparent">NexFlo AI</h1>
        <p class="text-lg text-gray-600 mt-2">
          Empower your call center with AI agents that handle inbound calls seamlessly. Setup a virtual agent below and pair them with your IVR call flows to deliver exceptional customer experiences.
        </p>
      </div>

      <!-- Agent Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Agent Card 1 -->
        <div class="glow relative p-1 rounded-2xl shadow-md hover:shadow-lg transition-all">
            <div class="flex flex-col h-full relative bg-gradient-to-r from-blue-100 to-purple-100 rounded-xl p-6">
            <div class="flex justify-center mb-4">
              <img :src="'/images/avatars/black-woman-1.png'" alt="Receptionist Avatar" class="w-16 h-16 rounded-full object-cover object-top" />
            </div>
            <div class="text-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Receptionist</h3>
              <p class="text-sm text-gray-600">Friendly & Welcoming</p>
            </div>
            <p class="flex-grow text-sm text-gray-700 mb-4 text-center">Handles front-desk calls with a friendly tone, directs calls to appropriate agents and groups, provides basic information such as business hours and locations</p>
            <button class="w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 hover:scale-105 active:scale-100 cursor-pointer transition-all">
              Setup Agent
            </button>
          </div>
        </div>

        <!-- Agent Card 2 -->
        <div class="glow relative p-1 rounded-2xl shadow-md hover:shadow-lg transition-all">
          <div class="flex flex-col h-full relative bg-gradient-to-r from-blue-100 to-purple-100 rounded-xl p-6">
            <div class="flex justify-center mb-4">
                <img :src="'/images/avatars/black-woman-2.png'" alt="Customer Support Specialist" class="w-16 h-16 rounded-full object-cover object-top" />
            </div>
            <div class="text-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Customer Support Specialist</h3>
              <p class="text-sm text-gray-600">Professional & Solution-Oriented</p>
            </div>
            <p class="flex-grow text-sm text-gray-700 mb-4 text-center">Provides answers to customer inquiries by studying company knowledge base (website, social pages, FAQs), handles complaints, engages specialists where necessary</p>
            <button class="w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 hover:scale-105 active:scale-100 cursor-pointer transition-all">
              Setup Agent
            </button>
          </div>
        </div>

        <!-- Agent Card 3 -->
        <div class="glow relative p-1 rounded-2xl shadow-md hover:shadow-lg transition-all">
          <div class="flex flex-col h-full relative bg-gradient-to-r from-blue-100 to-purple-100 rounded-xl p-6">
            <div class="flex justify-center mb-4">
                <img :src="'/images/avatars/black-man-1.png'" alt="Technical Support Specialist" class="w-16 h-16 rounded-full object-cover object-top" />
            </div>
            <div class="text-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Technical Support Specialist</h3>
              <p class="text-sm text-gray-600">Tech-Savvy & Solution-Oriented</p>
            </div>
            <p class="flex-grow text-sm text-gray-700 mb-4 text-center">Guides callers through troubleshooting technical issues, provides clear instructions for software or hardware problems, and escalates complex cases to advanced support teams when needed</p>
            <button class="w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 hover:scale-105 active:scale-100 cursor-pointer transition-all">
              Setup Agent
            </button>
          </div>
        </div>

      </div>

    </div>

  </template>

  <script>
  export default {
    data() {
      return {};
    },
  };
  </script>

  <style scoped>
  .glow {
    position: relative;
    overflow: hidden; /* Clip the rotating pseudo-element */
  }

  .glow::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 500px; /* Large enough to cover the card when rotated */
    height: 500px; /* Taller to ensure the gradient sweeps around the border */
    background: linear-gradient(
      90deg,
      #67e8f9 0%,
      #7dd3fc 5.88%,
      #93c5fd 11.76%,
      #818cf8 17.65%,
      #c4b5fd 23.53%,
      #d8b4fe 29.41%,
      #f0abfc 35.29%,
      #f472b6 41.18%,
      #fb7185 47.06%,
      #f472b6 52.94%,
      #f0abfc 58.82%,
      #d8b4fe 64.71%,
      #c4b5fd 70.59%,
      #818cf8 76.47%,
      #93c5fd 82.35%,
      #7dd3fc 88.24%,
      #67e8f9 94.12%
    );
    transform-origin: center center; /* Rotate around the card’s center */
    animation: rotate 10s linear infinite; /* Rotate continuously */
  }

  @keyframes rotate {
    from {
      transform: translate(-50%, -50%) rotate(0deg);
    }
    to {
      transform: translate(-50%, -50%) rotate(360deg);
    }
  }
  </style>
