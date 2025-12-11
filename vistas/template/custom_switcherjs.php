<!-- Custom-Switcher JS -->
<script src="../assets/js/custom-switcher.min.js"></script>

<!-- Custom JS -->
<script src="../assets/js/custom.js"></script>

<script>

  // $(".btn-modal-effect").each(function() {
  //   $(this).click(function(e) { e.preventDefault(); let effect = $(this).data('bs-effect'); $(".modal-effect").addClass(effect); });
  // });

  // $('.modal-effect').on('hidden.bs.modal', function(e) {
  //   let removeClass = $(this).attr('class').match(/(^|\s)effect-\S+/g); removeClass = removeClass[0].trim(); $(this).removeClass(removeClass);
  // });

  // Foco en el buscador de Select2
  $(document).on('select2:open', () => {  document.querySelector('.select2-search__field').focus(); });
  
</script>

<?php if (isset($_SESSION['user_update_sistema'])) {  ?>
  <?php if ($_SESSION['user_update_sistema'] == 1) {  ?>
  
  <?php } else  {  ?>
    <script>
      Swal.fire({
        title: "¿Has actualizado?",
        icon: "question",
        html: `¡Existe una Nueva actualización en el sistema de facturación! Descubre las últimas mejoras y características ahora disponibles. <br> 
        link: <a class="text-blue" href="https://chromewebstore.google.com/detail/clear-cache/cppjkneekbjaeellbfkmgnhonkkjfpdn?hl=es" target="_blank">https://chromewebstore.google.com/detail/clear-cache</a> 
        <br><br>
        <img src="../assets/video/clear_cache_tutorial.gif" alt="img" width="400px"> 
        `,        
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-check-circle"></i> Ok, ya actualzé!',
        cancelButtonText: '<i class="fa fa-thumbs-down"></i> Recuérdame más tarde.',
        
      }).then((result) => {
        if (result.isConfirmed) {
          $.getJSON(`../ajax/usuario.php?op=update_sistema`, { id_tabla: localStorage.getItem("nube_id_usuario")},   function (e, textStatus, jqXHR) {
              console.log(e);
          });
        } else {
          toastr_info('Recuerde!!', 'Para dejar de recibir este mensaje, haz clic en: Ok, ya actualicé.', 700);
        }
      });
    </script>
  <?php }  ?>

<?php } else  {  ?>
  <script>
     Swal.fire({
      title: '<strong>Tu sesion se ha terminado!!</strong>',
      icon: 'info',
      html: `Inicia <b>sesion</b> nuevamente , <a href="login.php">Salir.</a>`,
      showCloseButton: true,
      showCancelButton: true,
      focusConfirm: false,
      confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Salir!',
      confirmButtonAriaLabel: 'Thumbs up, great!',
      cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
      cancelButtonAriaLabel: 'Thumbs down'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire('Saliendo...', '<i class="fas fa-spinner fa-pulse"></i> Redireccionando...', 'success');
        window.location.href = `${window.location.host =='localhost' || es_numero(parseFloat(window.location.host)) == true ?`${window.location.origin}/libreria_sistema/vistas/`:window.location.origin}/vistas/`;
      } else {
        Swal.fire('Cerrando sesion', '<i class="fas fa-spinner fa-pulse"></i> De igual manera vamos a cerrar la sesión, jijijiji...', 'success');
        window.location.href = `${window.location.host =='localhost' || es_numero(parseFloat(window.location.host)) == true ?`${window.location.origin}/libreria_sistema/vistas/`:window.location.origin}/vistas/`;
      }
    });
  </script>
<?php }?>