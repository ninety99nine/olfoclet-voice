<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Hi Julian</h1>
                    <p class="text-lg text-gray-600">Today's Overview - {{ formattedDate }}</p>
                </div>
                <div class="flex space-x-3">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all">
                        Get Reports
                    </button>
                    <button class="px-4 py-2 bg-indigo-100 text-indigo-800 rounded-lg hover:bg-indigo-200 transition-all">
                        View Calls
                    </button>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Service Level -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <Gauge size="24" class="h-6 w-6 text-green-600"></Gauge>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Service Level (20s)</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ todayMetrics.serviceLevel }}%</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.serviceLevel, yesterdayMetrics.serviceLevel)">
                            <span v-if="getChangeDirection(todayMetrics.serviceLevel, yesterdayMetrics.serviceLevel) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.serviceLevel, yesterdayMetrics.serviceLevel) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.serviceLevel, yesterdayMetrics.serviceLevel) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- First Call Resolution (FCR) -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <Check size="24" class="h-6 w-6 text-green-600"></Check>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">First Call Resolution</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ todayMetrics.fcr }}%</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.fcr, yesterdayMetrics.fcr)">
                            <span v-if="getChangeDirection(todayMetrics.fcr, yesterdayMetrics.fcr) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.fcr, yesterdayMetrics.fcr) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.fcr, yesterdayMetrics.fcr) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Average Handle Time (AHT) -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <Timer size="24" class="h-6 w-6 text-indigo-600"></Timer>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Avg. Handle Time</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ formatTime(todayMetrics.aht) }}</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.aht, yesterdayMetrics.aht)">
                            <span v-if="getChangeDirection(todayMetrics.aht, yesterdayMetrics.aht) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.aht, yesterdayMetrics.aht) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.aht, yesterdayMetrics.aht) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Average Call Duration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <Timer size="24" class="h-6 w-6 text-indigo-600"></Timer>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Avg. Call Duration</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ formatTime(todayMetrics.avgCallDuration) }}</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.avgCallDuration, yesterdayMetrics.avgCallDuration)">
                            <span v-if="getChangeDirection(todayMetrics.avgCallDuration, yesterdayMetrics.avgCallDuration) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.avgCallDuration, yesterdayMetrics.avgCallDuration) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.avgCallDuration, yesterdayMetrics.avgCallDuration) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Average Wait Time -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <Timer size="24" class="h-6 w-6 text-indigo-600"></Timer>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Avg. Wait Time</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ formatTime(todayMetrics.avgWaitTime) }}</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.avgWaitTime, yesterdayMetrics.avgWaitTime)">
                            <span v-if="getChangeDirection(todayMetrics.avgWaitTime, yesterdayMetrics.avgWaitTime) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.avgWaitTime, yesterdayMetrics.avgWaitTime) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.avgWaitTime, yesterdayMetrics.avgWaitTime) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Call Abandonment Rate -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <Frown size="24" class="h-6 w-6 text-red-600"></Frown>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Call Abandonment Rate</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ todayMetrics.abandonmentRate }}%</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.abandonmentRate, yesterdayMetrics.abandonmentRate)">
                            <span v-if="getChangeDirection(todayMetrics.abandonmentRate, yesterdayMetrics.abandonmentRate) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.abandonmentRate, yesterdayMetrics.abandonmentRate) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.abandonmentRate, yesterdayMetrics.abandonmentRate) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Customer Satisfaction (CSAT) -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <Smile size="24" class="h-6 w-6 text-green-600"></Smile>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Customer Satisfaction</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ todayMetrics.csat }}%</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.csat, yesterdayMetrics.csat)">
                            <span v-if="getChangeDirection(todayMetrics.csat, yesterdayMetrics.csat) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.csat, yesterdayMetrics.csat) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.csat, yesterdayMetrics.csat) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- CSR Utilization -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <UsersRound size="24" class="h-6 w-6 text-indigo-600"></UsersRound>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">CSR Utilization</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ todayMetrics.csrUtilization }}%</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.csrUtilization, yesterdayMetrics.csrUtilization)">
                            <span v-if="getChangeDirection(todayMetrics.csrUtilization, yesterdayMetrics.csrUtilization) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.csrUtilization, yesterdayMetrics.csrUtilization) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.csrUtilization, yesterdayMetrics.csrUtilization) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Occupancy -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <Activity size="24" class="h-6 w-6 text-indigo-600"></Activity>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Occupancy</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ todayMetrics.occupancy }}%</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.occupancy, yesterdayMetrics.occupancy)">
                            <span v-if="getChangeDirection(todayMetrics.occupancy, yesterdayMetrics.occupancy) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.occupancy, yesterdayMetrics.occupancy) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.occupancy, yesterdayMetrics.occupancy) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Calls Today -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <Phone size="24" class="h-6 w-6 text-indigo-600"></Phone>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Calls Today</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ todayMetrics.totalCalls }}</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.totalCalls, yesterdayMetrics.totalCalls)">
                            <span v-if="getChangeDirection(todayMetrics.totalCalls, yesterdayMetrics.totalCalls) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.totalCalls, yesterdayMetrics.totalCalls) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.totalCalls, yesterdayMetrics.totalCalls) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Missed Calls -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <Phone size="24" class="h-6 w-6 text-red-600"></Phone>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Missed Calls</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ todayMetrics.missedCalls }}</p>
                        <p class="text-xs mt-1 flex items-center"
                           :class="getChangeClass(todayMetrics.missedCalls, yesterdayMetrics.missedCalls)">
                            <span v-if="getChangeDirection(todayMetrics.missedCalls, yesterdayMetrics.missedCalls) === 'increase'">↑</span>
                            <span v-else-if="getChangeDirection(todayMetrics.missedCalls, yesterdayMetrics.missedCalls) === 'decrease'">↓</span>
                            {{ getPercentageChange(todayMetrics.missedCalls, yesterdayMetrics.missedCalls) }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Calls Per Hour Chart -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Calls Per Hour</h3>
                <div class="h-64">
                    <LineChart v-if="callsPerHourData" :data="callsPerHourData" :options="chartOptions" />
                </div>
            </div>

            <!-- Queue Analysis Chart -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Queue Analysis</h3>
                <div class="h-64">
                    <BarChart v-if="queueAnalysisData" :data="queueAnalysisData" :options="queueChartOptions" />
                </div>
            </div>

            <!-- Agent Performance: Calls Handled -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Agent Calls Handled</h3>
                <div class="h-64">
                    <BarChart v-if="agentCallsHandledData" :data="agentCallsHandledData" :options="chartOptions" />
                </div>
            </div>

            <!-- Agent Performance: Avg. Handle Time -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Agent Avg. Handle Time</h3>
                <div class="h-64">
                    <BarChart v-if="agentHandleTimeData" :data="agentHandleTimeData" :options="chartOptions" />
                </div>
            </div>

            <!-- Agent Performance: Longest Handle Time -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Agent Longest Handle Time</h3>
                <div class="h-64">
                    <BarChart v-if="agentLongestHandleTimeData" :data="agentLongestHandleTimeData" :options="chartOptions" />
                </div>
            </div>

            <!-- Agent Performance: FCR -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Agent First Call Resolution</h3>
                <div class="h-64">
                    <BarChart v-if="agentFcrData" :data="agentFcrData" :options="chartOptions" />
                </div>
            </div>

            <!-- IVR Performance Chart -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">IVR Performance</h3>
                <div class="h-64">
                    <BarChart v-if="ivrPerformanceData" :data="ivrPerformanceData" :options="queueChartOptions" />
                </div>
            </div>

            <!-- Top Complaints Chart -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Complaints</h3>
                <div class="h-64">
                    <PieChart v-if="topComplaintsData" :data="topComplaintsData" :options="chartOptions" />
                </div>
            </div>

            <!-- Agent After-Call Work (ACW) Trend -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Agent After-Call Work (ACW) Trend</h3>
                <div class="h-64">
                    <LineChart v-if="acwTrendData" :data="acwTrendData" :options="chartOptions" />
                </div>
            </div>

            <!-- Call Resolution Status Breakdown -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Call Resolution Status</h3>
                <div class="h-64">
                    <PieChart v-if="callResolutionData" :data="callResolutionData" :options="chartOptions" />
                </div>
            </div>

            <!-- Service Level vs. SLA Adherence -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Level vs. SLA Adherence</h3>
                <div class="h-64">
                    <LineChart v-if="serviceLevelVsSlaData" :data="serviceLevelVsSlaData" :options="chartOptions" />
                </div>
            </div>

            <!-- Additional Insights (VOC and Quality Metrics) -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Voice of the Customer & Quality Metrics</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">C-Sat Score</p>
                        <p class="text-xl font-semibold text-gray-900">89%</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">D-Sat Score</p>
                        <p class="text-xl font-semibold text-gray-900">11%</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Transaction Quality</p>
                        <p class="text-xl font-semibold text-gray-900">92%</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-500">Recent VOC Feedback:</p>
                    <p class="text-gray-900">"Quick resolution, very satisfied with the agent’s help."</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Call Activity</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-indigo-50">
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Caller</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Agent</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Duration</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Queue Time</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="n in 5" :key="n" class="border-t border-gray-200 hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">+267 123 4567</td>
                                <td class="px-6 py-4 text-sm text-gray-900">John Doe</td>
                                <td class="px-6 py-4 text-sm text-gray-900">4m 32s</td>
                                <td class="px-6 py-4 text-sm text-gray-900">15s</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          :class="n % 2 === 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ n % 2 === 0 ? 'Resolved' : 'Unresolved' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">10:45 AM</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Gauge, Check, Timer, Frown, Smile, UsersRound, Activity, Phone } from 'lucide-vue-next';
import { Line as LineChart, Bar as BarChart, Pie as PieChart } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    BarElement,
    PieController,
    ArcElement,
    CategoryScale,
    LinearScale,
    PointElement,
} from 'chart.js';

// Register Chart.js components
ChartJS.register(Title, Tooltip, Legend, LineElement, BarElement, PieController, ArcElement, CategoryScale, LinearScale, PointElement);

export default {
    components: {
        LineChart,
        BarChart,
        PieChart,
        Gauge, Check, Timer, Frown, Smile, UsersRound, Activity, Phone
    },
    data() {
        return {
            formattedDate: new Date().toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            }),
            todayMetrics: {
                serviceLevel: 87, // Percentage
                fcr: 78, // Percentage
                aht: 312, // Seconds
                avgCallDuration: 180, // Seconds (placeholder: talk time portion of AHT)
                avgWaitTime: 25, // Seconds (from Queue Analysis chart)
                abandonmentRate: 4.5, // Percentage
                csat: 89, // Percentage
                csrUtilization: 82, // Percentage
                occupancy: 76, // Percentage
                totalCalls: 1245, // Count
                missedCalls: 62, // Placeholder: ~5% of total calls
            },
            yesterdayMetrics: {
                serviceLevel: 85, // Increased
                fcr: 74, // Decreased
                aht: 300, // Increased
                avgCallDuration: 170, // Increased (yesterday's value)
                avgWaitTime: 28, // Decreased (yesterday's value)
                abandonmentRate: 5.0, // Decreased
                csat: 88, // Increased
                csrUtilization: 85, // Decreased
                occupancy: 74, // Increased
                totalCalls: 1150, // Increased
                missedCalls: 70, // Decreased (yesterday's value)
            },
            // Placeholder data for Calls Per Hour (Line Chart) - 24-hour cycle
            callsPerHourData: {
                labels: [
                    '12 AM', '1 AM', '2 AM', '3 AM', '4 AM', '5 AM', '6 AM', '7 AM',
                    '8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM',
                    '4 PM', '5 PM', '6 PM', '7 PM', '8 PM', '9 PM', '10 PM', '11 PM'
                ],
                datasets: [
                    {
                        label: 'Calls Per Hour',
                        data: [
                            10, 8, 5, 3, 2, 5, 10, 20, // 12 AM to 7 AM (lower volumes at night)
                            50, 75, 120, 150, 200, 180, 160, 140, // 8 AM to 3 PM (peak business hours)
                            110, 90, 80, 70, 60, 50, 40, 30 // 4 PM to 11 PM (gradual decline)
                        ],
                        borderColor: '#4f46e5', // Indigo-600
                        backgroundColor: 'rgba(79, 70, 229, 0.2)', // Indigo-600 with opacity
                        fill: true,
                        tension: 0.4,
                    },
                ],
            },
            // Placeholder data for Queue Analysis (Bar Chart)
            queueAnalysisData: {
                labels: ['Waiting Calls', 'Avg. Wait Time (s)', 'Abandoned Calls'],
                datasets: [
                    {
                        label: 'Queue Metrics',
                        data: [30, 25, 15],
                        backgroundColor: ['#4f46e5', '#3b82f6', '#ef4444'], // Indigo (Volume), Blue (Time), Red (Negative)
                        borderColor: ['#4f46e5', '#3b82f6', '#ef4444'],
                        borderWidth: 1,
                    },
                ],
            },
            // Placeholder data for Agent Performance: Calls Handled (Bar Chart)
            agentCallsHandledData: {
                labels: ['John Doe', 'Jane Smith', 'Mike Brown', 'Sarah Lee', 'Tom Wilson'],
                datasets: [
                    {
                        label: 'Calls Handled',
                        data: [45, 38, 52, 30, 42],
                        backgroundColor: '#4f46e5', // Indigo-600
                        borderColor: '#4f46e5',
                        borderWidth: 1,
                    },
                ],
            },
            // Placeholder data for Agent Performance: Avg. Handle Time (Bar Chart)
            agentHandleTimeData: {
                labels: ['John Doe', 'Jane Smith', 'Mike Brown', 'Sarah Lee', 'Tom Wilson'],
                datasets: [
                    {
                        label: 'Avg. Handle Time (s)',
                        data: [312, 280, 340, 260, 300],
                        backgroundColor: '#a5b4fc', // Indigo-300
                        borderColor: '#a5b4fc',
                        borderWidth: 1,
                    },
                ],
            },
            // Placeholder data for Agent Performance: Longest Handle Time (Bar Chart)
            agentLongestHandleTimeData: {
                labels: ['John Doe', 'Jane Smith', 'Mike Brown', 'Sarah Lee', 'Tom Wilson'],
                datasets: [
                    {
                        label: 'Longest Handle Time (s)',
                        data: [420, 390, 480, 360, 450], // Placeholder data for longest handle times
                        backgroundColor: '#a5b4fc', // Indigo-300, matching Avg. Handle Time
                        borderColor: '#a5b4fc',
                        borderWidth: 1,
                    },
                ],
            },
            // Placeholder data for Agent Performance: FCR (Bar Chart)
            agentFcrData: {
                labels: ['John Doe', 'Jane Smith', 'Mike Brown', 'Sarah Lee', 'Tom Wilson'],
                datasets: [
                    {
                        label: 'FCR (%)',
                        data: [80, 75, 85, 70, 78],
                        backgroundColor: '#c7d2fe', // Indigo-200
                        borderColor: '#c7d2fe',
                        borderWidth: 1,
                    },
                ],
            },
            // Placeholder data for IVR Performance (Bar Chart)
            ivrPerformanceData: {
                labels: ['Time in IVR (s)', 'Dropped in IVR', 'Diverted to Agents'],
                datasets: [
                    {
                        label: 'IVR Metrics',
                        data: [120, 50, 800],
                        backgroundColor: ['#3b82f6', '#ef4444', '#4f46e5'], // Blue (Time), Red (Negative), Indigo (Volume)
                        borderColor: ['#3b82f6', '#ef4444', '#4f46e5'],
                        borderWidth: 1,
                    },
                ],
            },
            // Placeholder data for Top Complaints (Pie Chart)
            topComplaintsData: {
                labels: ['Billing Issues', 'Network Problems', 'Service Delays', 'Product Inquiries', 'Other'],
                datasets: [
                    {
                        label: 'Top Complaints',
                        data: [35, 25, 20, 15, 5],
                        backgroundColor: ['#ef4444', '#f97316', '#eab308', '#10b981', '#6b7280'], // Red, Orange, Yellow, Green, Gray
                        borderColor: ['#ef4444', '#f97316', '#eab308', '#10b981', '#6b7280'],
                        borderWidth: 1,
                    },
                ],
            },
            // Placeholder data for Agent After-Call Work (ACW) Trend (Line Chart)
            acwTrendData: {
                labels: ['8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM'],
                datasets: [
                    {
                        label: 'Avg. ACW (s)',
                        data: [45, 50, 55, 60, 65, 60, 58, 55, 50, 48],
                        borderColor: '#a5b4fc', // Indigo-300
                        backgroundColor: 'rgba(165, 180, 252, 0.2)', // Indigo-300 with opacity
                        fill: true,
                        tension: 0.4,
                    },
                ],
            },
            // Placeholder data for Call Resolution Status Breakdown (Pie Chart)
            callResolutionData: {
                labels: ['Resolved', 'Unresolved'],
                datasets: [
                    {
                        label: 'Call Resolution Status',
                        data: [75, 25],
                        backgroundColor: ['#10b981', '#ef4444'], // Green (Resolved), Red (Unresolved)
                        borderColor: ['#10b981', '#ef4444'],
                        borderWidth: 1,
                    },
                ],
            },
            // Placeholder data for Service Level vs. SLA Adherence (Line Chart)
            serviceLevelVsSlaData: {
                labels: ['8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM'],
                datasets: [
                    {
                        label: 'Service Level (%)',
                        data: [85, 87, 88, 90, 87, 86, 85, 88, 87, 86],
                        borderColor: '#10b981', // Green-600
                        backgroundColor: 'rgba(16, 185, 129, 0.2)', // Green-600 with opacity
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'SLA Adherence (%)',
                        data: [88, 89, 90, 92, 91, 90, 89, 91, 90, 89],
                        borderColor: '#4f46e5', // Indigo-600
                        backgroundColor: 'rgba(79, 70, 229, 0.2)', // Indigo-600 with opacity
                        fill: true,
                        tension: 0.4,
                    },
                ],
            },
            // Chart options for line and agent performance charts
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                            },
                            color: '#1f2937', // Gray-800
                        },
                    },
                    tooltip: {
                        backgroundColor: '#1f2937', // Gray-800
                        titleFont: { size: 14 },
                        bodyFont: { size: 12 },
                        padding: 10,
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#6b7280', // Gray-500
                            maxRotation: 45, // Rotate labels to 45 degrees
                            minRotation: 45, // Ensure labels are consistently rotated
                        },
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        ticks: {
                            color: '#6b7280', // Gray-500
                            beginAtZero: true,
                        },
                        grid: {
                            color: '#e5e7eb', // Gray-200
                        },
                    },
                },
            },
            // Chart options for queue analysis (stacked bar chart)
            queueChartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                            },
                            color: '#1f2937', // Gray-800
                        },
                    },
                    tooltip: {
                        backgroundColor: '#1f2937', // Gray-800
                        titleFont: { size: 14 },
                        bodyFont: { size: 12 },
                        padding: 10,
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#6b7280', // Gray-500
                        },
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        ticks: {
                            color: '#6b7280', // Gray-500
                            beginAtZero: true,
                        },
                        grid: {
                            color: '#e5e7eb', // Gray-200
                        },
                    },
                },
            },
        };
    },
    methods: {
        formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}m ${remainingSeconds}s`;
        },
        getPercentageChange(today, yesterday) {
            if (yesterday === 0) return 'N/A';
            const change = ((today - yesterday) / yesterday) * 100;
            return Math.abs(change).toFixed(1);
        },
        getChangeDirection(today, yesterday) {
            if (today > yesterday) return 'increase';
            if (today < yesterday) return 'decrease';
            return 'no-change';
        },
        getChangeClass(today, yesterday) {
            const direction = this.getChangeDirection(today, yesterday);
            if (direction === 'increase') {
                // For metrics where increase is good (Service Level, FCR, CSAT, Occupancy, Total Calls, CSR Utilization)
                if (['serviceLevel', 'fcr', 'csat', 'occupancy', 'totalCalls', 'csrUtilization'].some(metric => today === this.todayMetrics[metric])) {
                    return 'text-green-600';
                }
                // For metrics where increase is bad (AHT, Abandonment Rate, Avg Wait Time, Avg Call Duration, Missed Calls)
                return 'text-red-600';
            } else if (direction === 'decrease') {
                // For metrics where decrease is good (AHT, Abandonment Rate, Avg Wait Time, Avg Call Duration, Missed Calls)
                if (['aht', 'abandonmentRate', 'avgWaitTime', 'avgCallDuration', 'missedCalls'].some(metric => today === this.todayMetrics[metric])) {
                    return 'text-green-600';
                }
                // For metrics where decrease is bad (Service Level, FCR, CSAT, Occupancy, Total Calls, CSR Utilization)
                return 'text-red-600';
            }
            return 'text-gray-600';
        },
    },
};
</script>

<style>
/* Add any custom styles if needed */
</style>
