<template>
    <div class="min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-6">

      <!-- Page Header -->
      <div class="space-y-1">

        <div class="flex items-end space-x-2">
          <Headset size="48" stroke-width="1" class="text-gray-400" />
          <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Call Center</h2>
            <p class="text-sm text-gray-600 dark:text-neutral-400">
              Monitor live call activity and user workload
            </p>
          </div>
        </div>

        <!-- Tabs -->
        <Tabs v-model="activeTab" :tabs="tabs" />

      </div>

      <!-- Metrics -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <div class="text-sm text-gray-500 mb-1">Incoming Calls</div>
          <div class="text-3xl font-semibold text-gray-800">12</div>
          <div class="text-xs text-gray-400">Average wait: 2m 45s</div>
          <Button type="primary" size="sm" class="mt-3 w-full">Answer Next Call</Button>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <div class="text-sm text-gray-500 mb-1">Outgoing Calls</div>
          <div class="text-3xl font-semibold text-gray-800">8</div>
          <div class="text-xs text-gray-400">Today's total</div>
          <Button type="primary" size="sm" class="mt-3 w-full">Make New Call</Button>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
          <div class="text-sm text-gray-500 mb-1">Missed Calls</div>
          <div class="text-3xl font-semibold text-gray-800">3</div>
          <div class="text-xs text-red-400">Requires callback</div>
          <Button type="primary" size="sm" class="mt-3 w-full">View Missed Calls</Button>
        </div>

      </div>

      <!-- Active Call Queue Table -->
      <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">

        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="text-2xl font-semibold text-gray-900">Active Call Queue</h3>
          </div>
          <Button type="outline" size="sm">Filters</Button>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Caller</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Queue</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wait Time</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SLA</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agents</th>
                <th class="px-4 py-3"></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr v-for="(call, i) in queue" :key="i">
                <td class="px-4 py-3">
                  <Pill :type="statusType(call.status)" size="xs">{{ call.status }}</Pill>
                </td>
                <td class="px-4 py-3">{{ call.caller }}</td>
                <td class="px-4 py-3">{{ call.phone }}</td>
                <td class="px-4 py-3">{{ call.queue }}</td>
                <td class="px-4 py-3">{{ call.wait }}</td>
                <td class="px-4 py-3">
                  <span :class="call.sla === 'On Target' ? 'text-green-500 text-sm' : 'text-yellow-500 text-sm'">
                    {{ call.sla }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-500">No agents</td>
                <td class="px-4 py-3">
                  <Button type="primary" size="sm">Answer</Button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </template>

  <script>
  import Tabs from '@Partials/Tabs.vue';
  import Button from '@Partials/Button.vue';
  import Pill from '@Partials/Pill.vue';
  import { Headset } from 'lucide-vue-next';

  export default {
    components: { Tabs, Button, Pill, Headset },
    data() {
      return {
        activeTab: 'overview',
        tabs: [
          { label: 'Overview', value: 'overview' },
          { label: 'Queues', value: 'queues' },
          { label: 'Agents', value: 'agents' },
          { label: 'Metrics', value: 'metrics' },
        ],
        queue: [
          { status: 'Incoming', caller: 'John Smith', phone: '+1 (555) 123-4567', queue: 'Sales', wait: '0:45', sla: 'On Target' },
          { status: 'Waiting', caller: 'Sarah Johnson', phone: '+1 (555) 234-5678', queue: 'Support', wait: '2:15', sla: 'On Target' },
          { status: 'Ringing', caller: 'Michael Brown', phone: '+1 (555) 345-6789', queue: 'Customer Service', wait: '1:05', sla: 'On Target' },
          { status: 'Waiting', caller: 'Emily Davis', phone: '+1 (555) 456-7890', queue: 'Technical', wait: '3:30', sla: 'Warning' },
          { status: 'Incoming', caller: 'Robert Wilson', phone: '+1 (555) 567-8901', queue: 'Sales', wait: '0:30', sla: 'On Target' }
        ]
      }
    },
    methods: {
      statusType(status) {
        if (status === 'Incoming') return 'success';
        if (status === 'Waiting') return 'warning';
        if (status === 'Ringing') return 'primary';
        return 'default';
      }
    }
  }
  </script>
