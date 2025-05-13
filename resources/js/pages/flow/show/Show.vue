<template>
    <div class="bg-gray-50">
        <!-- Performance Bar -->
        <div class="bg-gray-50 pt-4 px-2 relative z-20">
            <div class="w-full flex items-center rounded-2xl shadow-md border bg-gray-50 border-gray-200 overflow-hidden mb-4">
                <div class="flex flex-grow pl-8">
                    <!-- Total Calls -->
                    <div class="flex-1 flex items-center space-x-3">
                        <Phone class="w-6 h-6 text-indigo-600" />
                        <div>
                            <p class="text-md font-semibold text-gray-900">27</p>
                            <p class="text-sm text-gray-500">Total Calls</p>
                        </div>
                    </div>
                    <!-- Avg. Handle Time -->
                    <div class="flex-1 flex items-center space-x-3">
                        <Clock class="w-6 h-6 text-indigo-600" />
                        <div>
                            <p class="text-md font-semibold text-gray-900">5m 12s</p>
                            <p class="text-sm text-gray-500">Avg. Handle Time</p>
                        </div>
                    </div>
                    <!-- Avg. Call Duration -->
                    <div class="flex-1 flex items-center space-x-3">
                        <PhoneCall class="w-6 h-6 text-indigo-600" />
                        <div>
                            <p class="text-md font-semibold text-gray-900">3m 15s</p>
                            <p class="text-sm text-gray-500">Avg. Call Duration</p>
                        </div>
                    </div>
                    <!-- Avg. Wait Time -->
                    <div class="flex-1 flex items-center space-x-3">
                        <Timer class="w-6 h-6 text-red-600" />
                        <div>
                            <p class="text-md font-semibold text-red-600">0m 25s</p>
                            <p class="text-sm text-gray-500">Avg. Wait Time</p>
                        </div>
                    </div>
                    <!-- First Call Resolution -->
                    <div class="flex-1 flex items-center space-x-3">
                        <CheckCircle class="w-6 h-6 text-green-600" />
                        <div>
                            <p class="text-md font-semibold text-green-600">86%</p>
                            <p class="text-sm text-gray-500">First Call Resolution</p>
                        </div>
                    </div>
                </div>
                <!-- Outbound Button -->
                <div class="bg-gray-100 border-l border-gray-200 text-sm text-gray-500 hover:text-indigo-600 hover:bg-indigo-100 active:bg-indigo-200">
                    <button class="flex items-center space-x-2 py-6 px-8 active:scale-95 cursor-pointer">
                        <Redo size="16" />
                        <span>Outbound</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex h-[calc(100vh-180px)] px-2 space-x-2 relative z-10">
            <!-- Left Panel: Conversation List -->
            <div class="w-1/4 rounded-2xl shadow-md border border-gray-200 overflow-y-auto">
                <transition name="fade-1" mode="out-in">
                    <template v-if="isAnswered">
                        <div class="select-none h-full">
                            <transition name="fade-1" mode="out-in">
                                <template v-if="isWrappingUp">
                                    <div class="flex flex-col items-center h-full">
                                        <div class="w-full py-2.5 text-sm text-center font-medium text-gray-900 bg-gray-100 border-b border-gray-200 mb-4">
                                            <p>Wrap Up</p>
                                        </div>
                                        <div class="flex flex-col items-center flex-grow">
                                            <p class="text-xs text-gray-500 mb-2">Contact Mood</p>
                                            <div class="w-full flex justify-evenly text-2xl mb-4">
                                                <div :class="['w-8 h-8 flex items-center justify-center border-2 rounded-full hover:bg-indigo-100 hover:border-indigo-300 hover:scale-125 transition-all cursor-pointer', selectedMood == 1 ? 'bg-indigo-100 border-indigo-300' : 'border-transparent']" @click="selectedMood = 1">😡</div>
                                                <div :class="['w-8 h-8 flex items-center justify-center border-2 rounded-full hover:bg-indigo-100 hover:border-indigo-300 hover:scale-125 transition-all cursor-pointer', selectedMood == 2 ? 'bg-indigo-100 border-indigo-300' : 'border-transparent']" @click="selectedMood = 2">😟</div>
                                                <div :class="['w-8 h-8 flex items-center justify-center border-2 rounded-full hover:bg-indigo-100 hover:border-indigo-300 hover:scale-125 transition-all cursor-pointer', selectedMood == 3 ? 'bg-indigo-100 border-indigo-300' : 'border-transparent']" @click="selectedMood = 3">😐</div>
                                                <div :class="['w-8 h-8 flex items-center justify-center border-2 rounded-full hover:bg-indigo-100 hover:border-indigo-300 hover:scale-125 transition-all cursor-pointer', selectedMood == 4 ? 'bg-indigo-100 border-indigo-300' : 'border-transparent']" @click="selectedMood = 4">😀</div>
                                                <div :class="['w-8 h-8 flex items-center justify-center border-2 rounded-full hover:bg-indigo-100 hover:border-indigo-300 hover:scale-125 transition-all cursor-pointer', selectedMood == 5 ? 'bg-indigo-100 border-indigo-300' : 'border-transparent']" @click="selectedMood = 5">😍</div>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">Call Tags</p>
                                            <div class="w-full px-2 mb-4">
                                                <SelectTags
                                                    v-model="tags"
                                                    :allowCustom="false"
                                                    :isDraggable="false"
                                                    :options="callTagOptions"
                                                    placeholder="Select call tags"
                                                    :searchableFields="['label', 'value']" />
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">Call Notes</p>
                                            <div class="w-full px-2 mb-4">
                                                <Input v-model="contactNotes" type="textarea" rows="2" placeholder="Contact notes"></Input>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">Outcome</p>
                                            <div class="flex justify-center space-x-2 mb-4">
                                                <div class="bg-indigo-500 text-white hover:bg-gray-300 rounded-full px-3 py-1.5 text-xs font-medium cursor-pointer transition-all">Resolved</div>
                                                <div class="bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full px-3 py-1.5 text-xs font-medium cursor-pointer transition-all">Unresolved</div>
                                                <div class="bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full px-3 py-1.5 text-xs font-medium cursor-pointer transition-all">Follow-Up</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4 pb-8">
                                            <button @click="dropCall" class="flex items-center rotate-[135deg] justify-center w-16 h-16 text-white rounded-full shadow-lg bg-red-600 hover:bg-red-700 cursor-pointer transition-all hover:scale-110 active:scale-100">
                                                <Phone size="32" stroke-width="0" fill="#ffffff"></Phone>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div>
                                        <div class="flex items-center justify-between bg-gray-100 border-b border-gray-200 py-2.5 px-8">
                                            <div class="flex items-center justify-center space-x-2">
                                                <div class="w-fit p-1 bg-red-300 rounded-full animate-pulse">
                                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                                </div>
                                                <span class="text-xs text-red-500 font-bold">Recording</span>
                                            </div>
                                            <div class="flex items-center justify-center space-x-2">
                                                <Pause class="h-4 w-4 text-gray-500 cursor-pointer hover:text-indigo-500 hover:scale-125 active:scale-100 transition-all" />
                                                <CircleStop class="h-4 w-4 text-gray-500 cursor-pointer hover:text-indigo-500 hover:scale-125 active:scale-100 transition-all" />
                                            </div>
                                        </div>
                                        <div class="w-full flex justify-evenly bg-gray-100 text-gray-500 border-b border-gray-200 py-3 text-xs">
                                            <div class="flex flex-col items-center">
                                                <h6>Waiting</h6>
                                                <p class="font-bold">00:35</p>
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <h6>Holding</h6>
                                                <p class="font-bold">00:25</p>
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <h6>Transferred</h6>
                                                <p class="font-bold">2</p>
                                            </div>
                                        </div>
                                        <!-- Tabs -->
                                        <div class="flex border-b border-gray-200">
                                            <button class="flex-1 py-2 px-3 text-xs font-medium text-gray-900 bg-gray-100 border-b-2 border-indigo-600 hover:bg-gray-200 cursor-pointer">Current Call</button>
                                            <button class="flex-1 py-2 px-3 text-xs font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 cursor-pointer">Call History</button>
                                        </div>
                                        <div class="flex flex-col items-center pt-4">
                                            <div class="w-14 h-14 flex items-center justify-center bg-gray-100 border-2 border-gray-200 rounded-full mb-2">
                                                <UserRound size="28" class="text-gray-300" />
                                            </div>
                                            <div class="mb-4">
                                                <p class="text-sm text-center text-gray-700">{{ conversations[selectedConversation].name }}</p>
                                                <p class="text-xs text-center text-gray-500">{{ conversations[selectedConversation].sender }}</p>
                                            </div>
                                            <div class="flex items-center justify-center space-x-2 mb-4">
                                                <Phone class="text-gray-500" size="16" />
                                                <p class="text-xs text-gray-500">02:25</p>
                                            </div>
                                            <div class="grid grid-cols-3 gap-x-2 mb-4">
                                                <button class="w-16 h-16 p-3 rounded-full cursor-pointer flex flex-col items-center justify-center text-gray-600 border border-transparent hover:border-gray-200 hover:shadow hover:scale-105 active:scale-100 transition-all">
                                                    <MicOff class="h-5 w-5" />
                                                    <span class="text-xs whitespace-nowrap mt-1">Mute</span>
                                                </button>
                                                <button class="w-16 h-16 p-3 rounded-full cursor-pointer flex flex-col items-center justify-center text-gray-600 border border-transparent hover:border-gray-200 hover:shadow hover:scale-105 active:scale-100 transition-all">
                                                    <Pause class="h-5 w-5" />
                                                    <span class="text-xs text-gray-600 whitespace-nowrap mt-1">Hold</span>
                                                </button>
                                                <button class="w-16 h-16 p-3 rounded-full cursor-pointer flex flex-col items-center justify-center text-gray-600 border border-transparent hover:border-gray-200 hover:shadow hover:scale-105 active:scale-100 transition-all">
                                                    <ArrowRightLeft class="h-5 w-5" />
                                                    <span class="text-xs text-gray-600 whitespace-nowrap mt-1">Transfer</span>
                                                </button>
                                                <button class="w-16 h-16 p-3 rounded-full cursor-pointer flex flex-col items-center justify-center text-gray-600 border border-transparent hover:border-gray-200 hover:shadow hover:scale-105 active:scale-100 transition-all">
                                                    <UserPlus class="h-5 w-5" />
                                                    <span class="text-xs text-gray-600 whitespace-nowrap mt-1">Add Call</span>
                                                </button>
                                                <button class="w-16 h-16 p-3 rounded-full cursor-pointer flex flex-col items-center justify-center text-gray-600 border border-transparent hover:border-gray-200 hover:shadow hover:scale-105 active:scale-100 transition-all">
                                                    <Tag class="h-5 w-5" />
                                                    <span class="text-xs text-gray-600 whitespace-nowrap mt-1">Tag</span>
                                                </button>
                                                <button class="w-16 h-16 p-3 rounded-full cursor-pointer flex flex-col items-center justify-center text-gray-600 border border-transparent hover:border-gray-200 hover:shadow hover:scale-105 active:scale-100 transition-all">
                                                    <MessageSquare class="h-5 w-5" />
                                                    <span class="text-xs text-gray-600 whitespace-nowrap mt-1">Sms</span>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">Call Tags</p>
                                            <div class="flex justify-center space-x-2 mb-4">
                                                <div class="bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full px-2 py-0.5 text-xs font-medium cursor-pointer transition-all">Setswana</div>
                                                <div class="bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full px-2 py-0.5 text-xs font-medium cursor-pointer transition-all">Billing Issue</div>
                                            </div>
                                            <p class="text-xs text-gray-500 mb-2">Contact Mood</p>
                                            <div class="w-full flex justify-evenly text-2xl mb-4">
                                                <div :class="['w-8 h-8 flex items-center justify-center border-2 rounded-full hover:bg-indigo-100 hover:border-indigo-300 hover:scale-125 transition-all cursor-pointer', selectedMood == 1 ? 'bg-indigo-100 border-indigo-300' : 'border-transparent']" @click="selectedMood = 1">😡</div>
                                                <div :class="['w-8 h-8 flex items-center justify-center border-2 rounded-full hover:bg-indigo-100 hover:border-indigo-300 hover:scale-125 transition-all cursor-pointer', selectedMood == 2 ? 'bg-indigo-100 border-indigo-300' : 'border-transparent']" @click="selectedMood = 2">😟</div>
                                                <div :class="['w-8 h-8 flex items-center justify-center border-2 rounded-full hover:bg-indigo-100 hover:border-indigo-300 hover:scale-125 transition-all cursor-pointer', selectedMood == 3 ? 'bg-indigo-100 border-indigo-300' : 'border-transparent']" @click="selectedMood = 3">😐</div>
                                                <div :class="['w-8 h-8 flex items-center justify-center border-2 rounded-full hover:bg-indigo-100 hover:border-indigo-300 hover:scale-125 transition-all cursor-pointer', selectedMood == 4 ? 'bg-indigo-100 border-indigo-300' : 'border-transparent']" @click="selectedMood = 4">😀</div>
                                                <div :class="['w-8 h-8 flex items-center justify-center border-2 rounded-full hover:bg-indigo-100 hover:border-indigo-300 hover:scale-125 transition-all cursor-pointer', selectedMood == 5 ? 'bg-indigo-100 border-indigo-300' : 'border-transparent']" @click="selectedMood = 5">😍</div>
                                            </div>
                                            <div class="w-full px-2 mb-4">
                                                <Input v-model="contactNotes" type="textarea" rows="1" placeholder="Contact notes"></Input>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <button @click="wrapUp" class="flex items-center justify-center space-x-2 text-white rounded-full shadow-lg px-6 py-3 bg-indigo-500 hover:bg-indigo-600 cursor-pointer transition-all hover:scale-110 active:scale-100">
                                                    <span>Wrap Up</span>
                                                    <MoveRight class="h-6 w-6 mt-0.5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </transition>
                        </div>
                    </template>
                    <template v-else>
                        <div>
                            <!-- Tags -->
                            <SelectTags
                                class="p-2"
                                v-model="tags"
                                :allowCustom="false"
                                :isDraggable="false"
                                :options="tagOptions"
                                placeholder="Select channels to show"
                                :searchableFields="['label', 'value']" />
                            <!-- Conversation List -->
                            <div class="divide-y-4 divide-gray-500 p-2 space-y-1">
                                <div v-for="(conversation, index) in conversations" :key="index" @click="selectConversation(index)"
                                    :class="[
                                        'px-3 py-2.5 rounded-xl border hover:shadow-md hover:border-gray-200 hover:bg-gray-100 cursor-pointer transition-all duration-300',
                                        selectedConversation === index ? 'shadow-md border-gray-200 bg-gray-100' : 'border-transparent'
                                    ]">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <img v-if="conversation.channel === 'X'" :src="'/images/channels/x.png'" alt="Twitter Logo" class="w-6 h-6 object-cover" />
                                            <img v-else-if="conversation.channel === 'SMS'" :src="'/images/channels/sms.png'" alt="Twitter Logo" class="w-6 h-6 object-cover" />
                                            <img v-else-if="conversation.channel === 'Email'" :src="'/images/channels/email.png'" alt="Twitter Logo" class="w-6 h-6 object-cover" />
                                            <img v-else-if="conversation.channel === 'WhatsApp'" :src="'/images/channels/whatsApp.png'" alt="Twitter Logo" class="w-6 h-6 object-cover" />
                                            <img v-else-if="conversation.channel === 'Telegram'" :src="'/images/channels/telegram.png'" alt="Twitter Logo" class="w-6 h-6 object-cover" />
                                            <img v-else-if="conversation.channel === 'Instagram'" :src="'/images/channels/instagram.png'" alt="Twitter Logo" class="w-6 h-6 object-cover" />
                                            <div v-else-if="conversation.channel === 'Call'" class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium bg-indigo-200">
                                                <img :src="'/images/calling.gif'" class="w-4" />
                                            </div>
                                        </div>
                                        <div class="flex-1 relative">
                                            <div class="flex justify-between items-center">
                                                <p class="text-sm font-medium text-gray-900">{{ conversation.name || conversation.sender }}</p>
                                                <p class="text-xs text-gray-500">{{ conversation.timestamp }}</p>
                                            </div>
                                            <p class="w-44 text-xs text-gray-500 truncate">{{ conversation.snippet }}</p>
                                            <div v-if="conversation.channel === 'Call' && conversation.state == 'critical'" class="w-2 h-2 bg-red-500 rounded-full absolute bottom-1 right-0"></div>
                                            <div v-if="conversation.channel === 'Call' && conversation.state == 'warning'" class="w-2 h-2 bg-yellow-500 rounded-full absolute bottom-1 right-0"></div>
                                            <div v-if="conversation.channel === 'Call' && conversation.state == 'normal'" class="w-2 h-2 bg-green-500 rounded-full absolute bottom-1 right-0"></div>
                                        </div>
                                        <div v-if="conversation.unread" class="flex-shrink-0">
                                            <span class="h-2 w-2 bg-green-500 rounded-full"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </transition>
            </div>

            <!-- Middle Panel: Conversation View -->
            <div class="w-1/2 flex flex-col">
                <!-- Dialer for Incoming Call -->
                <div v-if="conversations[selectedConversation].channel === 'Call'"
                    :class="['relative rounded-2xl shadow-xl shadow-fuchsia-100 transition sucking-all duration-1000', isAnswered ? '-mt-40 mb-0 opacity-0' : 'mt-0 mb-6 opacity-100']">
                    <div class="absolute top-0 left-0 right-0 bottom-0 overflow-hidden rounded-2xl">
                        <div class="glow top-1/2"></div>
                    </div>
                    <div class="relative bg-slate-100 rounded-2xl space-y-4 m-0.5">
                        <!-- Conversation Header -->
                        <div class="pt-4 px-6 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <img :src="'/images/calling.gif'" class="w-8" />
                                <div class="w-8 h-8 flex items-center justify-center text-3xl">😀</div>
                                <div>
                                    <p class="font-semibold">{{ conversations[selectedConversation].name }}</p>
                                    <p class="text-xs">{{ conversations[selectedConversation].sender }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end space-y-1 text-right">
                                <p class="text-xs text-gray-500 mr-2">Waiting: {{ conversations[selectedConversation].waitingTime }}</p>
                                <p class="bg-gray-200 text-gray-600 rounded-full px-3 py-1 text-xs font-medium cursor-pointer transition-all">Queue: Sales Support</p>
                                <p class="text-gray-500 text-sm">Transfer from Bonolo Tabona</p>
                            </div>
                        </div>
                        <div class="flex justify-end items-center px-6 pb-4">
                            <div class="flex space-x-4">
                                <button @click="answerCall" class="flex items-center justify-center h-10 px-4 text-white rounded-full shadow-md bg-blue-500 hover:bg-blue-600 cursor-pointer transition-all hover:scale-110 active:scale-100 space-x-1">
                                    <IterationCcw size="16"></IterationCcw>
                                    <span class="text-sm">Next Call</span>
                                </button>
                                <button @click="answerCall" class="flex items-center justify-center h-10 px-4 text-white rounded-full shadow-md bg-blue-500 hover:bg-blue-600 cursor-pointer transition-all hover:scale-110 active:scale-100 space-x-1">
                                    <ArrowRightLeft size="16"></ArrowRightLeft>
                                    <span class="text-sm">Transfer</span>
                                </button>
                                <button @click="answerCall" class="flex items-center justify-center h-10 px-4 text-white rounded-full shadow-md bg-blue-500 hover:bg-blue-600 cursor-pointer transition-all hover:scale-110 active:scale-100 space-x-1">
                                    <MoveUp size="16"></MoveUp>
                                    <span class="text-sm">Escalate</span>
                                </button>
                                <button @click="dropCall" class="flex items-center rotate-[135deg] justify-center w-10 h-10 text-white rounded-full shadow-lg bg-red-600 hover:bg-red-700 cursor-pointer transition-all hover:scale-110 active:scale-100">
                                    <Phone size="20" stroke-width="0" fill="#ffffff"></Phone>
                                </button>
                                <button @click="answerCall" class="flex items-center justify-center w-10 h-10 text-white rounded-full shadow-md bg-green-600 hover:bg-green-700 cursor-pointer transition-all hover:scale-110 active:scale-100">
                                    <Phone size="20" stroke-width="0" fill="#ffffff"></Phone>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs (Pills) - Sticky at the top -->
                <div class="bg-white rounded-t-2xl shadow-sm border border-gray-200 py-3">
                    <div class="flex justify-center space-x-2">
                        <button @click="selectTab('Script')" :class="selectedTab === 'Script' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="rounded-full px-4 py-1.5 text-sm font-medium cursor-pointer transition-all">Script</button>
                        <button @click="selectTab('Calls')" :class="selectedTab === 'Calls' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="rounded-full px-4 py-1.5 text-sm font-medium cursor-pointer transition-all">Calls</button>
                        <button @click="selectTab('SMS')" :class="selectedTab === 'SMS' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="rounded-full px-4 py-1.5 text-sm font-medium cursor-pointer transition-all">SMS</button>
                        <button @click="selectTab('Whatsapp')" :class="selectedTab === 'Whatsapp' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="rounded-full px-4 py-1.5 text-sm font-medium cursor-pointer transition-all">Whatsapp</button>
                        <button @click="selectTab('Email')" :class="selectedTab === 'Email' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="rounded-full px-4 py-1.5 text-sm font-medium cursor-pointer transition-all">Email</button>
                    </div>
                </div>

                <div class="shadow-md border border-gray-200 overflow-y-auto flex-grow rounded-b-2xl">
                    <!-- Conversation Header -->
                    <div v-if="conversations[selectedConversation].channel != 'Call'" class="p-3 border-b border-gray-200 flex items-center space-x-3">
                        <span class="shadow inline-flex items-center justify-center h-10 w-10 rounded-full text-xs font-medium" :class="conversations[selectedConversation].badgeClass">{{ conversations[selectedConversation].name.split('')[0] }}</span>
                        <div>
                            <p class="font-semibold">{{ conversations[selectedConversation].name }}</p>
                            <p class="text-xs">{{ conversations[selectedConversation].sender }}</p>
                        </div>
                    </div>

                    <!-- Scrollable Content -->
                    <div class="p-3 overflow-y-auto flex-grow" :class="[{ 'bg-slate-50' : conversations[selectedConversation].channel === 'SMS' }, { 'whatsapp-background': conversations[selectedConversation].channel === 'WhatsApp' }]">
                        <!-- Script Section -->
                        <div v-if="selectedTab === 'Script'" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-4">
                            <!-- Greeting -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Greeting</h3>
                                <p class="text-sm text-gray-700 text-justify">Hello, thank you for contacting Orange Botswana. My name is Julian Tabona, and I’m here to assist you today.</p>
                            </div>
                            <!-- Verification -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Verification</h3>
                                <p class="text-sm text-gray-700 mb-2">To ensure I’m speaking with the right person, please confirm your details:</p>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm text-gray-700">First Name:</label>
                                        <input type="text" :value="conversations[selectedConversation].name.split(' ')[0]" placeholder="Enter first name" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 mt-1">
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-700">Last Name:</label>
                                        <input type="text" :value="conversations[selectedConversation].name.split(' ')[1]" placeholder="Enter last name" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 mt-1">
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-700">National ID:</label>
                                        <input type="text" value="01234567" placeholder="Enter national ID" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 mt-1">
                                    </div>
                                </div>
                            </div>
                            <!-- Issue Resolution -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Issue Resolution</h3>
                                <p class="text-sm text-gray-700 mb-2">Thank you for sharing this information with me. How can I assist you today?</p>
                                <p class="flex items-center space-x-2 bg-blue-50 rounded-lg text-sm text-gray-700 p-2 mb-4">
                                    <Clock size="16"></Clock>
                                    <span>Wait for the customer’s response.</span>
                                </p>
                                <h3 class="text-base font-semibold text-gray-900 mb-2">Select Issue</h3>
                                <div class="flex space-x-2 mb-4">
                                    <div class="bg-indigo-500 whitespace-nowrap text-white hover:bg-gray-300 rounded-full px-3 py-1.5 text-xs font-medium cursor-pointer transition-all">Billing Inquiry</div>
                                    <div class="bg-gray-200 whitespace-nowrap text-gray-700 hover:bg-gray-300 rounded-full px-3 py-1.5 text-xs font-medium cursor-pointer transition-all">Payment Issue</div>
                                    <div class="bg-gray-200 whitespace-nowrap text-gray-700 hover:bg-gray-300 rounded-full px-3 py-1.5 text-xs font-medium cursor-pointer transition-all">Balance Discrepancy</div>
                                    <div class="bg-gray-200 whitespace-nowrap text-gray-700 hover:bg-gray-300 rounded-full px-3 py-1.5 text-xs font-medium cursor-pointer transition-all">Refund Request</div>
                                </div>
                                <!-- Billing Inquiry Handling -->
                                <div class="mt-4">
                                    <h3 class="text-base font-semibold text-gray-900 mb-2">Billing Inquiry</h3>
                                    <p class="text-sm text-gray-700 mb-2">Could you please provide more details about the billing issue you are experiencing? For example, is it related to overcharging, an incorrect bill, or a service not reflected in your account?</p>
                                    <div class="space-y-3">
                                        <label class="text-sm text-gray-700">Description:</label>
                                        <textarea placeholder="Describe the billing issue" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 mt-1" rows="3"></textarea>
                                        <label class="text-sm text-gray-700">Billing Date:</label>
                                        <input type="date" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 mt-1">
                                        <label class="text-sm text-gray-700">Amount Charged:</label>
                                        <input type="text" placeholder="Enter amount" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 mt-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Messages based on Channel -->
                        <div v-else-if="selectedTab === 'Calls' && conversations[selectedConversation].channel === 'Call'" class="">
                            <!-- Call Channel Messages -->
                            <div class="mb-3 flex justify-center">
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Missed Call - 10:45 AM</span>
                            </div>
                            <div class="mb-3 flex justify-start">
                                <div class="max-w-md bg-gray-100 text-gray-900 text-sm rounded-lg p-2">
                                    <p>Hi, I called earlier but couldn’t get through. Can someone call me back?</p>
                                    <p class="text-xs text-gray-500 mt-1">10:46 AM</p>
                                </div>
                            </div>
                            <div v-if="isAnswered" class="mb-3 flex justify-end">
                                <div class="max-w-md bg-indigo-600 text-white text-sm rounded-lg p-2">
                                    <p>Sure, I’ll call you back shortly. Apologies for the inconvenience!</p>
                                    <p class="text-xs text-indigo-200 mt-1">10:48 AM</p>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="selectedTab === 'Email' && conversations[selectedConversation].channel === 'Email'">
                            <!-- Email Channel Messages -->
                            <div class="mb-3 flex justify-start">
                                <div class="max-w-md bg-gray-100 text-gray-900 text-sm rounded-lg p-2">
                                    <p class="text-xs text-gray-500 mb-1">From: john.doe@email.com</p>
                                    <p class="text-xs text-gray-500 mb-1">To: support@orangebotswana.com</p>
                                    <p>Billing Inquiry - Urgent</p>
                                    <p class="mt-1">Dear Support Team, I noticed an error in my latest bill. Can you please assist?</p>
                                    <p class="text-xs text-gray-500 mt-1">10:30 AM</p>
                                </div>
                            </div>
                            <div class="mb-3 flex justify-end">
                                <div class="max-w-md bg-indigo-600 text-white text-sm rounded-lg p-2">
                                    <p class="text-xs text-indigo-200 mb-1">From: support@orangebotswana.com</p>
                                    <p class="text-xs text-indigo-200 mb-1">To: john.doe@email.com</p>
                                    <p>Dear John, I’m looking into your billing issue and will get back to you shortly.</p>
                                    <p class="text-xs text-indigo-200 mt-1">10:32 AM</p>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="selectedTab === 'Whatsapp' && conversations[selectedConversation].channel === 'WhatsApp'">
                            <!-- WhatsApp Channel Messages -->
                            <div class="mb-3 flex justify-center">
                                <span class="text-xs text-gray-700 font-bold bg-white px-3 py-1 rounded-lg">Wed, 19 Mar</span>
                            </div>
                            <div class="mb-3 flex justify-start">
                                <div class="max-w-md bg-white text-gray-900 text-sm rounded-lg rounded-bl-none p-2 relative whatsapp-bubble-customer">
                                    <p>Need help with my plan. Can you suggest a better option?</p>
                                    <p class="text-xs text-gray-500 mt-1 text-right flex items-center justify-end">
                                        10:15 AM
                                    </p>
                                </div>
                            </div>
                            <div class="mb-3 flex justify-end">
                                <div class="max-w-md bg-green-200 text-gray-900 text-sm rounded-lg rounded-br-none p-2 relative whatsapp-bubble-agent">
                                    <p>Hi! I’d recommend our Family Plan for better value. I’ll send you the details.</p>
                                    <p class="text-xs text-gray-500 mt-1 text-right flex items-center justify-end">
                                        10:17 AM
                                        <CheckCheck size="16" class="text-gray-500 ml-1" />
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="selectedTab === 'SMS' && conversations[selectedConversation].channel === 'SMS'">
                            <!-- SMS Channel Messages -->
                            <div class="mb-3 flex justify-start">
                                <div class="max-w-md bg-gray-200 text-gray-900 text-sm rounded-lg p-2">
                                    <p>Network issue reported. No signal since morning.</p>
                                    <p class="text-xs text-gray-500 mt-1">10:00 AM</p>
                                </div>
                            </div>
                            <div class="mb-3 flex justify-end">
                                <div class="max-w-md bg-indigo-600 text-white text-sm rounded-lg p-2">
                                    <p>We’re investigating the network issue in your area. Will update you soon.</p>
                                    <p class="text-xs text-indigo-200 mt-1">10:02 AM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reply Input (Hidden during Incoming Call) -->
                <div class="rounded-b-2xl shadow-md p-3 bg-slate-50 border-t border-gray-200">
                    <div class="flex items-center space-x-2 mb-2">
                        <select class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-600">
                            <option>Reply via Call</option>
                            <option>Reply via Email</option>
                            <option>Reply via WhatsApp</option>
                            <option>Reply via SMS</option>
                        </select>
                        <button class="p-1 text-gray-500 hover:text-gray-700">
                            <Bold class="h-4 w-4" />
                        </button>
                        <button class="p-1 text-gray-500 hover:text-gray-700">
                            <Italic class="h-4 w-4" />
                        </button>
                        <button class="p-1 text-gray-500 hover:text-gray-700">
                            <Link class="h-4 w-4" />
                        </button>
                        <button class="p-1 text-gray-500 hover:text-gray-700">
                            <Smile class="h-4 w-4" />
                        </button>
                    </div>
                    <div class="flex space-x-2">
                        <textarea placeholder="Type your reply..." rows="4" class="flex-1 border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"></textarea>
                        <button class="max-h-10 px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all">
                            Send
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Details Sidebar -->
            <div class="w-1/4 flex flex-col rounded-2xl overflow-hidden shadow-md border border-gray-200">
                <!-- Tabs -->
                <div class="flex border-b border-gray-200">
                    <button
                        @click="selectRightTab('Details')"
                        :class="[
                            'flex-1 py-3 px-3 text-xs font-medium',
                            selectedRightTab === 'Details' ? 'text-gray-900 bg-gray-100 border-b-2 border-indigo-600' : 'text-gray-500 hover:bg-gray-100 cursor-pointer'
                        ]"
                    >
                        Details
                    </button>
                    <button
                        @click="selectRightTab('Copilot')"
                        :class="[
                            'flex-1 py-3 px-3 text-xs font-medium',
                            selectedRightTab === 'Copilot' ? 'text-gray-900 bg-gray-100 border-b-2 border-indigo-600' : 'text-gray-500 hover:bg-gray-100 cursor-pointer'
                        ]"
                    >
                        <span class="bg-gradient-to-r font-bold from-red-600 to-violet-600 inline-block text-transparent bg-clip-text">Copilot</span>
                    </button>
                </div>

                <!-- Details Tab Content -->
                <div v-if="selectedRightTab === 'Details'" class="p-4">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-sm font-semibold text-gray-900">Details</p>
                        <div class="flex items-center space-x-4">
                            <button class="flex items-center space-x-1 text-gray-500 hover:text-gray-700 cursor-pointer hover:underline">
                                <SquarePen class="h-3 w-3" />
                                <span class="text-xs">Edit</span>
                            </button>
                            <button class="text-gray-500 hover:text-gray-700">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                    <!-- Contact -->
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-1">Contact</p>
                        <p class="text-sm text-gray-500">First Name: <span class="text-gray-900">{{ conversations[selectedConversation].name.split(' ')[0] }}</span></p>
                        <p class="text-sm text-gray-500">Last Name: <span class="text-gray-900">{{ conversations[selectedConversation].name.split(' ')[1] }}</span></p>
                        <p class="text-sm text-gray-500">Phone: <span class="text-gray-900">{{ conversations[selectedConversation].sender }}</span></p>
                    </div>
                    <!-- Assignee -->
                    <div class="mb-3">
                        <p class="text-xs text-gray-500">Assignee</p>
                        <p class="text-sm text-gray-900">Julian Tabona</p>
                    </div>
                    <!-- User Notes -->
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-1">Previous Notes</p>
                        <div class="space-y-2">
                            <div class="flex justify-end">
                                <div class="max-w-2xl bg-gray-200 rounded-2xl px-4 py-2">
                                    <p class="text-xs">Billed twice in one day for All My Friends service</p>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <div class="max-w-2xl bg-gray-200 rounded-2xl px-4 py-2">
                                    <p class="text-xs">Wanted to know shortcode to check airtime balance</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Last Interaction Mood -->
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-2">Last Call Tags</p>
                        <div class="flex space-x-2 mb-4">
                            <div class="bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full px-2 py-0.5 text-xs font-medium cursor-pointer transition-all">Setswana</div>
                            <div class="bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-full px-2 py-0.5 text-xs font-medium cursor-pointer transition-all">Internet Issue</div>
                        </div>
                    </div>
                    <!-- Last Interaction Mood -->
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-1">Last Call Mood</p>
                        <p class="text-2xl text-gray-500">😟</p>
                    </div>
                    <!-- Last Interaction Outcome -->
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-2">Last Call Outcome</p>
                        <div class="w-fit bg-indigo-500 text-white hover:bg-gray-300 rounded-full px-3 py-1.5 text-xs font-medium cursor-pointer transition-all">Resolved</div>
                    </div>
                </div>

                <!-- Copilot Tab Content -->
                <div v-else-if="selectedRightTab === 'Copilot'" class="flex flex-col h-full bg-gray-50">
                    <!-- Chat Header -->
                    <div class="select-none flex justify-between items-center p-4 border-b border-gray-200">
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center space-x-1 bg-indigo-50 px-2 py-1 border border-dashed border-indigo-200 text-indigo-500 rounded-sm text-sm">
                                <BotIcon size="16" />
                                <span>Copilot</span>
                            </div>
                        </div>
                    </div>
                    <!-- Chat Area -->
                    <div class="p-4 space-y-4 overflow-y-auto h-2 flex-grow">
                        <div v-if="copilotMessages.length === 0" class="select-none flex flex-col items-center justify-center text-center h-full">
                            <BotIcon size="32" class="text-gray-400 mb-4 animate-bounce" />
                            <h3 class="text-sm font-semibold text-gray-800">Start a Conversation</h3>
                            <p class="text-xs text-gray-500 mt-2">Ask Copilot for suggestions or insights</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="(message, index) in copilotMessages" :key="index" class="space-y-2 animate-fade-in">
                                <!-- Agent Message -->
                                <div v-if="message.role === 'user'" class="flex justify-end">
                                    <div class="max-w-xs bg-indigo-500 text-white rounded-2xl rounded-br-none px-3 py-2">
                                        <p class="text-xs" v-html="renderMarkdown(message.content)"></p>
                                    </div>
                                </div>
                                <!-- Copilot Message -->
                                <div v-if="message.role === 'assistant'" class="flex justify-start items-start space-x-2">
                                    <BotIcon size="16" class="text-gray-600 mt-2" />
                                    <div class="max-w-xs bg-gray-100 border border-gray-200 rounded-2xl rounded-bl-none px-3 py-2">
                                        <p class="text-xs text-gray-900 markdown-content" v-html="renderMarkdown(message.content)"></p>
                                        <p class="text-xs text-gray-500 mt-1">{{ formattedDatetime(message.timestamp) }}</p>
                                        <!-- Sources -->
                                        <div v-if="message.context && message.context.length > 0" class="mt-2">
                                            <button
                                                @click="toggleContext(index)"
                                                class="text-xs text-blue-600 hover:underline focus:outline-none transition-colors duration-200"
                                            >
                                                {{ expandedContext[index] ? 'Hide Sources' : 'Show Sources' }}
                                            </button>
                                            <div v-if="expandedContext[index]" class="mt-2 space-y-2">
                                                <div v-for="(ctx, ctxIndex) in message.context" :key="ctxIndex" class="bg-gray-200 p-2 rounded-md shadow-inner">
                                                    <p class="text-xs font-medium text-gray-700 flex items-center space-x-1">
                                                        <span>{{ ctx.title }}</span>
                                                        <span class="text-gray-500">({{ ctx.type }})</span>
                                                    </p>
                                                    <p class="text-xs text-gray-600 mt-1">{{ ctx.content }}</p>
                                                    <a v-if="ctx.url" :href="ctx.url" target="_blank" class="text-xs text-blue-600 hover:underline">View Source</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Thinking Message -->
                                <div v-if="message.role === 'thinking'" class="flex justify-start items-start space-x-2">
                                    <BotIcon size="16" class="text-gray-600 mt-2" />
                                    <div class="max-w-xs bg-gray-100 border border-gray-200 rounded-2xl rounded-bl-none px-3 py-2">
                                        <div class="typing-dots">
                                            <span class="dot">.</span>
                                            <span class="dot">.</span>
                                            <span class="dot">.</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Error Message -->
                                <div v-if="message.role === 'error'" class="flex justify-start items-start space-x-2">
                                    <AlertCircle size="16" class="text-red-500 mt-2" />
                                    <div class="max-w-xs bg-red-100 text-red-700 rounded-lg p-3">
                                        <p class="text-xs">{{ message.content }}</p>
                                        <p class="text-xs text-red-500 mt-1">{{ formattedDatetime(message.timestamp) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Input Area -->
                    <div class="p-4 border-t border-gray-200">
                        <form @submit.prevent="sendCopilotMessage" class="flex items-start space-x-2">
                            <Input
                                rows="2"
                                type="textarea"
                                class="w-full text-xs"
                                v-model="newCopilotMessage"
                                :disabled="isSendingCopilotMessage"
                                placeholder="Ask Copilot anything ..."
                                @keydown.enter.prevent="sendCopilotMessage"
                            />
                            <button
                                @click.prevent="sendCopilotMessage"
                                :class="[
                                    'select-none p-2 rounded-full',
                                    isSendingCopilotMessage || !newCopilotMessage.trim() ? 'bg-gray-100' : 'bg-indigo-500 hover:bg-indigo-400 hover:scale-110 hover:cursor-pointer active:scale-100'
                                ]"
                            >
                                <Loader v-if="isSendingCopilotMessage" size="16" class="text-white animate-spin"></Loader>
                                <ArrowUp v-else size="16" :class="[newCopilotMessage.trim() ? 'text-white' : 'text-gray-300']"></ArrowUp>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Input from '@Partials/Input.vue';
import SelectTags from '@Partials/SelectTags.vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import {
    Mail, MoveRight, MessageSquare, MessageSquareText, MoveUp, UserRound, SquarePen, Bold, Redo,
    Italic, Link, Smile, X, MicOff, Pause, CircleStop, ArrowRight, UserPlus, Tag, Moon, Phone,
    Check, CheckCheck, IterationCcw, ArrowRightLeft, Clock, PhoneCall, Timer, CheckCircle,
    BotIcon, ArrowUp, Loader, AlertCircle
} from 'lucide-vue-next';

export default {
    components: {
        Input,
        SelectTags,
        Mail, MoveRight, MessageSquare, MessageSquareText, MoveUp, UserRound, SquarePen, Redo, Bold,
        Italic, Link, Smile, X, MicOff, Pause, CircleStop, ArrowRight, UserPlus, Tag, Moon, Phone,
        Check, CheckCheck, IterationCcw, ArrowRightLeft, Clock, PhoneCall, Timer, CheckCircle,
        BotIcon, ArrowUp, Loader, AlertCircle
    },
    data() {
        return {
            tags: [],
            tagOptions: [
                { label: 'Call', value: 'call' },
                { label: 'SMS', value: 'sms' },
                { label: 'Whatsapp', value: 'whatsapp' },
                { label: 'Telegram', value: 'telegram' },
                { label: 'Email', value: 'email' },
            ],
            callTagOptions: [
                { label: 'Setswana', value: 'setswana' },
                { label: 'Billing Issue', value: 'billing issue' },
                { label: 'Technical Issue', value: 'technical issue' },
            ],
            selectedConversation: 0,
            selectedTab: 'Script',
            selectedRightTab: 'Details',
            isAnswered: false,
            isWrappingUp: false,
            selectedMood: 4,
            contactNotes: '',
            copilotMessages: [
                {
                    role: 'user',
                    content: 'All My Friends Bundles cost',
                    timestamp: new Date('2025-05-09T10:41:00')
                },
                {
                    role: 'assistant',
                    content: `The **All My Friends** bundles from Orange Botswana offer voice, SMS, and data with validity periods of 1, 7, or 30 days. Here are the costs and details:

- **1 Day Bundle**:
  - Cost: BWP 3
  - Includes: 30 minutes, 30 SMS, 30 MB
  - Subscription Code: *150*10#

- **7 Day Bundle**:
  - Cost: BWP 15
  - Includes: 150 minutes, 150 SMS, 150 MB
  - Subscription Code: *150*11#

- **30 Day Bundle**:
  - Cost: BWP 50
  - Includes: 500 minutes, 500 SMS, 500 MB
  - Subscription Code: *150*12#

You can subscribe by dialing the respective codes or via the Orange Yame portal. Would you like me to suggest a response for a customer inquiring about these bundles?`,
                    context: [
                        {
                            title: 'Orange Botswana - All My Friends Offer',
                            type: 'Webpage',
                            content: 'All My Friends is a prepaid offer with voice, SMS, and data bundles valid for 1, 7, or 30 days, starting at BWP 3 for 30 min/SMS/MB.',
                            url: 'https://www.orange.co.bw/en/prepaid-mobile-internet-offers/all-my-friends.html?offer=All++My++Friend+1Day'
                        }
                    ],
                    timestamp: new Date('2025-05-09T10:42:00')
                }
            ],
            newCopilotMessage: '',
            isSendingCopilotMessage: false,
            expandedContext: {},
            conversations: [
                {
                    name: 'Karabo Modise',
                    channel: 'Call',
                    badgeClass: 'bg-indigo-100 text-indigo-600',
                    sender: '+267 123 4567',
                    snippet: 'Missed call - Callback requested',
                    timestamp: '10:45 AM',
                    state: 'critical',
                    callStatus: 'incoming',
                    waitingTime: '24 secs'
                },
                {
                    name: 'Naledi Mosweu',
                    channel: 'Call',
                    badgeClass: 'bg-indigo-100 text-indigo-600',
                    sender: '+267 543 6743',
                    snippet: 'New call',
                    timestamp: '10:45 AM',
                    state: 'warning',
                    callStatus: 'incoming',
                    waitingTime: '24 secs'
                },
                {
                    name: 'Keneilwe Letsatsi',
                    channel: 'Call',
                    badgeClass: 'bg-indigo-100 text-indigo-600',
                    sender: '+267 098 7892',
                    snippet: 'New call',
                    timestamp: '10:45 AM',
                    state: 'normal',
                    callStatus: 'incoming',
                    waitingTime: '24 secs'
                },
                {
                    name: 'Richard Molefe',
                    channel: 'WhatsApp',
                    badgeClass: 'bg-green-100 text-green-600',
                    sender: '+267 987 6543',
                    snippet: 'How do i check airtime balance',
                    timestamp: '10:15 AM',
                    unread: false
                },
                {
                    name: 'Thapelo Letsholo',
                    channel: 'WhatsApp',
                    badgeClass: 'bg-green-100 text-green-600',
                    sender: '+267 987 6543',
                    snippet: 'Hello, my internet stopped working this morning',
                    timestamp: '10:15 AM',
                    unread: false
                },
                {
                    name: 'Moses Oduetse',
                    channel: 'Telegram',
                    badgeClass: 'bg-green-100 text-green-600',
                    sender: '+267 987 6543',
                    snippet: 'What do i dial to subscribe for All My Friends',
                    timestamp: '10:15 AM',
                    unread: false
                },
                {
                    name: 'Lesego Otsile',
                    channel: 'Instagram',
                    badgeClass: 'bg-green-100 text-green-600',
                    sender: '+267 987 6543',
                    snippet: 'Need help setting up internet bundles on new phone',
                    timestamp: '10:15 AM',
                    unread: false
                },
                {
                    name: 'Gorata Moesi',
                    channel: 'X',
                    badgeClass: 'bg-green-100 text-green-600',
                    sender: '+267 987 6543',
                    snippet: 'Hi, where do i check the freebie airtime balance',
                    timestamp: '10:15 AM',
                    unread: false
                },
                {
                    name: 'Jacob Odirile',
                    channel: 'SMS',
                    badgeClass: 'bg-blue-100 text-blue-600',
                    sender: '+267 555 1234',
                    snippet: 'Network issue reported',
                    timestamp: '10:00 AM',
                    unread: false
                },
                {
                    name: 'Moses Ofentse',
                    channel: 'Email',
                    badgeClass: 'bg-indigo-100 text-indigo-600',
                    sender: 'john.doe@email.com',
                    snippet: 'Billing inquiry - Urgent',
                    timestamp: '10:30 AM',
                    unread: false
                }
            ]
        };
    },
    methods: {
        selectConversation(index) {
            this.selectedConversation = index;
            this.isAnswered = false;
        },
        answerCall() {
            this.isAnswered = true;
        },
        dropCall() {
            this.isWrappingUp = false;
            this.isAnswered = false;
        },
        wrapUp() {
            this.isWrappingUp = true;
        },
        selectTab(tab) {
            this.selectedTab = tab;
        },
        selectRightTab(tab) {
            this.selectedRightTab = tab;
        },
        formattedDatetime(timestamp) {
            return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
        renderMarkdown(content) {
            const html = marked.parse(content);
            return DOMPurify.sanitize(html);
        },
        toggleContext(index) {
            this.expandedContext[index] = !this.expandedContext[index];
            this.$forceUpdate();
        },
        async sendCopilotMessage() {
            if (!this.newCopilotMessage.trim() || this.isSendingCopilotMessage) return;

            // Add agent's message
            const userMessage = {
                role: 'user',
                content: this.newCopilotMessage,
                timestamp: new Date()
            };
            this.copilotMessages.push(userMessage);

            // Add thinking message
            this.isSendingCopilotMessage = true;
            const thinkingMessage = {
                role: 'thinking',
                timestamp: new Date()
            };
            this.copilotMessages.push(thinkingMessage);

            const query = this.newCopilotMessage;
            this.newCopilotMessage = '';

            try {
                // Simulate API call (replace with real API call in production)
                await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate network delay
                const simulatedResponse = {
                    success: true,
                    response: `Got it! For "${query}", here's a suggestion: Please provide more details or specify if you need a response template, customer insights, or another action.`,
                    context: []
                };

                // Remove thinking message
                this.copilotMessages = this.copilotMessages.filter(msg => msg.role !== 'thinking');

                if (simulatedResponse.success) {
                    this.copilotMessages.push({
                        role: 'assistant',
                        content: simulatedResponse.response,
                        context: simulatedResponse.context,
                        timestamp: new Date()
                    });
                } else {
                    this.copilotMessages.push({
                        role: 'error',
                        content: simulatedResponse.message || 'Failed to get response from Copilot',
                        timestamp: new Date()
                    });
                }
            } catch (error) {
                // Remove thinking message on error
                this.copilotMessages = this.copilotMessages.filter(msg => msg.role !== 'thinking');
                this.copilotMessages.push({
                    role: 'error',
                    content: 'Failed to get response from Copilot',
                    timestamp: new Date()
                });
                console.error('Failed to query Copilot:', error);
            } finally {
                this.isSendingCopilotMessage = false;
            }
        }
    }
};
</script>

<style>
.whatsapp-background {
    background-image: url('/images/whatsapp/background.png');
    background-repeat: repeat;
    background-size: auto;
}

.whatsapp-bubble-customer {
    position: relative;
}

.whatsapp-bubble-customer::before {
    content: '';
    position: absolute;
    bottom: 0px;
    left: -11px;
    width: 15px;
    height: 16px;
    background: #ffffff;
    clip-path: polygon(90% 0, 90% 100%, 0 100%);
}

.whatsapp-bubble-agent {
    position: relative;
}

.whatsapp-bubble-agent::before {
    content: '';
    position: absolute;
    bottom: 0px;
    right: -12px;
    width: 15px;
    height: 16px;
    background: #b9f8cf;
    clip-path: polygon(0% 0, 70% 100%, 0 100%);
}

.glow {
    position: relative;
}

.glow::before {
    content: '';
    position: absolute;
    left: 50%;
    width: 650px;
    height: 650px;
    background: linear-gradient(
        90deg,
        #67e8f9 0%,
        #7dd3fc 7.6923%,
        #93c5fd 15.3846%,
        #c4b5fd 30.7692%,
        #d8b4fe 38.4615%,
        #f0abfc 46.1538%,
        #f0abfc 53.8462%,
        #d8b4fe 61.5385%,
        #c4b5fd 69.2308%,
        #93c5fd 84.6154%,
        #7dd3fc 92.3077%,
        #67e8f9 100%
    );
    transform-origin: center center;
    animation: rotate 10s linear infinite;
}

@keyframes rotate {
    from {
        transform: translate(-50%, -50%) rotate(0deg);
    }
    to {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

/* Copilot Chat Styles */
.markdown-content h1 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0.75rem 0 0.5rem;
    color: #1f2937;
}
.markdown-content h2 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0.5rem 0 0.25rem;
    color: #1f2937;
}
.markdown-content p {
    margin: 0.25rem 0;
    color: #374151;
}
.markdown-content ul,
.markdown-content ol {
    margin: 0.25rem 0;
    padding-left: 1rem;
}
.markdown-content li {
    margin: 0.125rem 0;
    color: #374151;
}
.markdown-content strong {
    font-weight: 700;
    color: #1f2937;
}
.markdown-content code {
    background-color: #f3f4f6;
    padding: 0.1rem 0.2rem;
    border-radius: 0.25rem;
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.75rem;
}
.markdown-content a {
    color: #2563eb;
    text-decoration: underline;
}
.markdown-content a:hover {
    color: #1d4ed8;
}

/* Typing dots animation */
.typing-dots {
    display: inline-flex;
    align-items: center;
    margin-top: -8px;
}
.typing-dots .dot {
    font-size: 1rem;
    line-height: 1;
    color: #6b7280;
    animation: blink 1.4s infinite both;
}
.typing-dots .dot:nth-child(2) {
    animation-delay: 0.2s;
}
.typing-dots .dot:nth-child(3) {
    animation-delay: 0.4s;
}
@keyframes blink {
    0% { opacity: 0.2; }
    20% { opacity: 1; }
    100% { opacity: 0.2; }
}
</style>
