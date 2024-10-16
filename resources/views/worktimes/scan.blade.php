<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Worktime_Scan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <button type="button" class="btn btn-neutral mt-3" onclick="window.location='{{ url('/') }}'">戻る　</button>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="scan p-6 text-gray-900">
                    <div style="width:500px;" id="reader"></div>

                    {{-- 読み取った従業員IDをPOST送信する --}}
                    <form id="attendance-form" method="POST" action="{{ route('worktimes.scan.post') }}">
                        @csrf
                        <input type="hidden" id="employee-id" name="employee_id">
                    </form>

                    {{-- シャッター音 --}}
                    <audio id="shutter-sound" src="{{ asset('sounds/shutter.mp3') }}" preload="auto"></audio>
                    {{-- POST送信成功時の音 --}}
                    <audio id="post-success-sound" src="{{ asset('sounds/success.mp3') }}" preload="auto"></audio>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            //シャッター音の再生
                            function playShutterSound() {
                                document.getElementById('shutter-sound').play();
                            }

                            //成功音の再生
                            function playPostSuccessSound() {
                                const successSound = document.getElementById('post-success-sound');
                                successSound.play().then(() => {
                                    console.log('成功音が再生されました。');
                                }).catch((e) => console.error('成功音再生に失敗:', e));
                            }

                            //スキャン中フラグ
                            let isScanning = false;

                            function onScanSuccess(qrCodeMessage) {
                                //スキャン中の場合、処理を停止
                                if (isScanning) return;

                                //スキャン開始
                                isScanning = true;
                                console.log(`QR Code Scanned: ${qrCodeMessage}`);

                                //QRコードから従業員IDを取得
                                const employeeId = qrCodeMessage.split('=')[1];
                                if (employeeId) {
                                    document.getElementById('employee-id').value = employeeId;

                                    //シャッター音再生
                                    playShutterSound();

                                    //2秒後にフォームを送信
                                    setTimeout(() => {
                                        document.getElementById('attendance-form').submit();
                                    }, 2000);
                                } else {
                                    console.error("従業員IDを取得できませんでした。");
                                    alert('無効なQRコードです。従業員IDが取得できません。');
                                    isScanning = false;
                                }
                            }

                            //スキャン後、5秒後にスキャン再開
                            setTimeout(() => {
                                isScanning = false;
                            }, 5000);

                            let lastError = null;

                            //読み取りに失敗した場合
                            function onScanFailure(error) {
                                if (error !== lastError) {
                                    console.warn(`QRコード読み取りエラー: ${error}`);
                                    lastError = error;
                                }
                            }

                            let html5QrcodeScanner = new Html5QrcodeScanner(
                                "reader", {
                                    fps: 10,
                                    qrbox: 300,
                                    aspectRatio: 1.0,
                                    disableFlip: false,
                                    qrCodeSuccessCallback: onScanSuccess,
                                    qrCodeErrorCallback: onScanFailure,
                                    rememberLastUsedCamera: true
                                });
                            html5QrcodeScanner.render(onScanSuccess, onScanFailure);

                            //セッションのエラーメッセージをポップアップ表示
                            @if (session('error'))
                                alert("{{ session('error') }}");
                            @endif
                        });

                        //POST送信が成功した場合に成功音を再生する
                        document.getElementById('attendance-form').addEventListener('submit', function(event) {
                            event.preventDefault();

                            let formData = new FormData(this);
                            fetch(this.action, {
                                method: 'POST',
                                body: formData,
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    playPostSuccessSound();
                                } else if (data.error) {
                                    alert(data.error);
                                    isScanning = false;
                                }
                            })
                            .catch(error => {
                                console.error('エラー:', error);
                                isScanning = false;
                            });
                        });
                    </script>
            </div>
        </div>
    </div>
</x-guest-layout>
