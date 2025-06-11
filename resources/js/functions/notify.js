export function notifyComplete()
{
  $.notify({
    // options
    message: '計算が完了しました'
  },{
    // settings
    type: 'success',
    placement: {
      from: "bottom",
      align: "center"
    },
    animate: {
      enter: 'animate__animated animate__fadeInDown',
      exit: 'animate__animated animate__fadeOutUp'
    },
  });
}
