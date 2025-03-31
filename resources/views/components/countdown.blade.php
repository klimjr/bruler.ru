<div
    class="flex gap-1 md:gap-2 items-center text-sm md:text-2xl text-white text-center font-extrabold bg-black rounded-[10px] px-[10px] md:px-[20px] py-[7px] md:py-[15px]">
    <svg class="w-[12px] md:w-full" width="18" height="24" viewBox="0 0 18 24" fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd"
            d="M4.25 4.75C4.25 2.12665 6.37665 0 9 0C11.6234 0 13.75 2.12665 13.75 4.75V5.01923C13.75 5.70959 13.1904 6.26923 12.5 6.26923C11.8096 6.26923 11.25 5.70959 11.25 5.01923V4.75C11.25 3.50736 10.2426 2.5 9 2.5C7.75736 2.5 6.75 3.50736 6.75 4.75V7.31055C7.46525 7.10825 8.21998 7 9 7C13.5563 7 17.25 10.6937 17.25 15.25C17.25 19.8063 13.5563 23.5 9 23.5C4.44365 23.5 0.75 19.8063 0.75 15.25C0.75 12.4522 2.14268 9.9797 4.27261 8.4879C4.25777 8.41088 4.25 8.33135 4.25 8.25V4.75ZM9 9.5C5.82436 9.5 3.25 12.0744 3.25 15.25C3.25 18.4256 5.82436 21 9 21C12.1756 21 14.75 18.4256 14.75 15.25C14.75 12.0744 12.1756 9.5 9 9.5ZM9 12.8333C9.69036 12.8333 10.25 13.393 10.25 14.0833V16.4167C10.25 17.107 9.69036 17.6667 9 17.6667C8.30964 17.6667 7.75 17.107 7.75 16.4167V14.0833C7.75 13.393 8.30964 12.8333 9 12.8333Z"
            fill="white" />
    </svg>
    <div id="countdown-{{ $productId }}"></div>
</div>

<script>
    let endDate{{ $productId }} = new Date("{{ $endDate }}").getTime();
    let nowDate{{ $productId }} = new Date("{{ $nowDate }}").getTime();
    let timeDifference{{ $productId }} = endDate{{ $productId }} - nowDate{{ $productId }}

    let countdownFunction{{ $productId }} = setInterval(function() {
        timeDifference{{ $productId }} = timeDifference{{ $productId }} - 1000

        let hours{{ $productId }} = timeDifference{{ $productId }} > 0 ? Math.floor(
            timeDifference{{ $productId }} / 1000 / 60 / 60) % 24 : 0;
        let minutes{{ $productId }} = timeDifference{{ $productId }} > 0 ? Math.floor(
            timeDifference{{ $productId }} / 1000 / 60) % 60 : 0;
        let seconds{{ $productId }} = timeDifference{{ $productId }} > 0 ? Math.floor(
            timeDifference{{ $productId }} / 1000) % 60 : 0;

        hours{{ $productId }} = hours{{ $productId }} < 10 ? "0" + hours{{ $productId }} :
            hours{{ $productId }};
        minutes{{ $productId }} = minutes{{ $productId }} < 10 ? "0" + minutes{{ $productId }} :
            minutes{{ $productId }};
        seconds{{ $productId }} = seconds{{ $productId }} < 10 ? "0" + seconds{{ $productId }} :
            seconds{{ $productId }};

        document.getElementById("countdown-{{ $productId }}").innerHTML = hours{{ $productId }} + ":" +
            minutes{{ $productId }} + ":" + seconds{{ $productId }}

        if (timeDifference{{ $productId }} < 0) {
            clearInterval(countdownFunction{{ $productId }});
            document.getElementById("countdown-{{ $productId }}").innerHTML = "Ещё немного!"
        }
    }, 1000);
</script>
