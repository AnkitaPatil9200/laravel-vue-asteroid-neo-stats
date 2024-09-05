<template>
    <div class="w-full h-screen main-div">
        <div class="bg-transparent relative z-0 invisible spinner">
            <div class="absolute inset-0 flex justify-center items-center z-10 ">
                <fwb-spinner size="12" />
            </div>
        </div>
        <div class="row">
            <div class="flex justify-center">
                <div
                    class="w-full max-w-sm p-1 m-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
                    <!-- Alert Message -->
                    <p :class="alertMessageClass">{{ alertMessage }}</p>
                    <!-- Form -->
                    <form class="space-y-6" @submit.prevent="getAsteroidsData">
                        <!-- Tailwind Datepicker -->
                        <vue-tailwind-datepicker v-model="dateValue" placeholder="Select Start and End Dates"
                            :shortcuts="false" separator=" to " :formatter="formatter" as-single use-range />
                        <button type="submit"
                            class="text-white bg-vtd-primary-500 hover:bg-vtd-primary-600 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-vtd-primary-600 dark:hover:bg-vtd-primary-600 focus:outline-none dark:focus:ring-vtd-primary-700">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row" v-if="isSuccessResponseReceived">
            <div class="flex justify-center">
                <!-- Card to display Fastest Asteroid data -->
                <div>
                    <a
                        class="m-4 block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Fastest Asteroid
                        </h5>
                        <p class="font-normal text-gray-700 dark:text-gray-400">ID: {{ fa_id }}</p>
                        <p class="font-normal text-gray-700 dark:text-gray-400">Speed: {{ fa_speed }} Km/Hr</p>
                        <p class="font-normal text-gray-700 dark:text-gray-400">Average Size: {{ fa_size }} Km</p>

                    </a>
                </div>
                <!-- Card to display Closest Asteroid data -->
                <div>
                    <a
                        class="m-4 block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Closest Asteroid
                        </h5>
                        <p class="font-normal text-gray-700 dark:text-gray-400">ID: {{ ca_id }}</p>
                        <p class="font-normal text-gray-700 dark:text-gray-400">Distance: {{ ca_distance }} Km</p>
                        <p class="font-normal text-gray-700 dark:text-gray-400">Average Size: {{ ca_size }} Km</p>

                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="flex justify-center ">
                <!-- Canvas to display chart -->
                <div class="w-6/12">
                    <canvas id="neoStatChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import { ref } from "vue";
import { FwbSpinner } from 'flowbite-vue'

const formatter = ref({
    date: 'DD-MM-YYYY',
    month: 'MMM',
})
</script>

<script>
// declare chart variable
var neoStatChart;

export default {
    // declare data propertirs
    data() {
        return {
            fa_id: '',
            fa_speed: '',
            fa_size: '',
            ca_id: '',
            ca_distance: '',
            ca_size: '',
            dateValue: [],
            alertMessage: '',
            alertMessageClass: '',
            isSuccessResponseReceived: false,
        }
    },
    // define methods
    methods: {
        // used asyc await to get data from backend
        async getAsteroidsData() {

            // show loading spinner
            var spinner = document.querySelector('.spinner');
            var mainDiv = document.querySelector('.main-div');
            spinner.classList.remove('invisible');
            mainDiv.classList.add('opacity-50');

            const response = await axios({
                method: 'post',
                url: '/get-asteroids-data',
                data: {
                    from: this.dateValue[0],
                    to: this.dateValue[1],
                }
            });

            console.log(response);
            if (response.data.status) { // success response

                // hide loading spinner
                spinner.classList.add('invisible');
                mainDiv.classList.remove('opacity-50');

                // display success alert message
                this.alertMessage = response.data.message;
                this.alertMessageClass = 'text-green-400';
                this.isSuccessResponseReceived = true;

                // place response data for fastest asteroid
                this.fa_id = response.data.data.fastest_asteroid_data.id;
                this.fa_speed = response.data.data.fastest_asteroid_data.speed;
                this.fa_size = response.data.data.fastest_asteroid_data.average_size;

                // place response data for closest asteroid
                this.ca_id =response.data.data.closest_asteroid_data.id;
                this.ca_distance = response.data.data.closest_asteroid_data.distance;
                this.ca_size = response.data.data.closest_asteroid_data.average_size;

                // load chart
                this.loadNeoStatChart(response.data.data.chart_data.x_axis_data, response.data.data.chart_data.y_axis_data);
            } else {

                // hide loading spinner
                spinner.classList.add('invisible');
                mainDiv.classList.remove('opacity-50');

                // clear stats
                this.fa_id = this.fa_speed = this.fa_size = this.ca_id = this.ca_distance = this.ca_size = '';
                this.isSuccessResponseReceived = false;

                // destroy previous instance of chart
                if (typeof neoStatChart !== "undefined") {
                    neoStatChart.destroy();
                }

                // display error alert message
                this.alertMessageClass = 'text-red-400';
                if (typeof (response.data.message) != "undefined" && response.data.message !== null) {
                    this.alertMessage = response.data.message;
                } else {
                    this.alertMessage = 'Something went wrong.';
                }
            }
        },

        loadNeoStatChart($x, $y) {

            // destroy previous instance of chart
            if (typeof neoStatChart !== "undefined") {
                neoStatChart.destroy();
            }

            // define context
            const ctx = document.getElementById('neoStatChart');

            // define config
            const config = {
                type: 'line',
                data: {
                    labels: $x,
                    datasets: [{
                        label: 'Number of Asteroids passing near the Earth',
                        data: $y,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }

            // draw chart
            neoStatChart = new Chart(ctx, config);
        }
    }
}
</script>
