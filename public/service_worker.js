// Web-Push
// Public base64 to Uint
function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

self.addEventListener('push', function(event) {

    // console.log(event);

    // var data = event.data.json();

    // console.log(data);
    // event.waitUntil(self.registration.showNotification(data.title, {
    //     body: data.body,
    //     ícone: data.ícon,
    //     tag: data.tag
    // }));
});

function ServiceWorkerInit() {

    if ('serviceWorker' in navigator) {

        if ('PushManager' in window) {

            new Promise(function (resolve, reject) {

                const permissionResult = Notification.requestPermission(function (result) {

                    resolve(result);
                });

                if (permissionResult) {

                    permissionResult.then(resolve, reject);

                    navigator.serviceWorker
                        .register('./service_worker.js', { scope: './' })
                        .then(function (registration) {

                            console.log('Service Worker registrado com sucesso.');

                            const subscribeOptions = {
                                userVisibleOnly: true,
                                applicationServerKey: urlBase64ToUint8Array(
                                'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBUYIHBQFLXYp5Nksh8U',
                                ),
                            }

                            return registration.pushManager.subscribe(subscribeOptions);
                        })
                        .then(async function (pushSubscription) {
                            // console.log(
                            //     'Received PushSubscription: ',
                            //     JSON.stringify(pushSubscription),
                            // );

                            // console.log(pushSubscription.toJSON());

                            var data = {
                                action: "subscription",
                                ...pushSubscription.toJSON()
                            }

                            // console.log(data);

                            response = await Post("service_worker.php", data);

                            // console.log(response);

                            //Sends information to server
                            return pushSubscription;
                        })
                        .catch(error => {

                            console.log('Service Worker falhou:', error);
                        });
                }

            }).then(function (permissionResult) {

                if (permissionResult !== 'granted') {

                    throw new Error("We weren't granted permission.");
                }
            });
        }
    }
}