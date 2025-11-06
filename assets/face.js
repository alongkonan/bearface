// assets/face.js - helper functions to load face-api models and compute descriptor
async function loadFaceModels(){
  // uses CDN-hosted models from jsdelivr (face-api models must be hosted); instruct user in README
  if (window.faceapiLoaded) return;
  await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
  await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
  await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
  window.faceapiLoaded = true;
}

function fileToImage(file){
  return new Promise((resolve,reject)=>{
    const reader = new FileReader();
    reader.onload = ()=>{
      const img = new Image();
      img.onload = ()=>resolve(img);
      img.src = reader.result;
    };
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

function dataUrlToImage(url){
  return new Promise((resolve)=>{
    const img = new Image(); img.onload = ()=>resolve(img); img.src = url;
  });
}

async function getFaceDescriptor(input){
  // input: HTMLImageElement, HTMLVideoElement, or canvas
  const options = new faceapi.TinyFaceDetectorOptions();
  const detection = await faceapi.detectSingleFace(input, options).withFaceLandmarks().withFaceDescriptor();
  if (!detection) return null;
  return detection.descriptor;
}
