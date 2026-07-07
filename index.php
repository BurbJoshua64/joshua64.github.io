<?php
// Variable para capturar los mensajes de éxito o error
$mensaje_alerta = "";

// PROCESAMIENTO DEL FORMULARIO (PHP)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = htmlspecialchars($_POST['nombre']);
    $email_cliente = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $detalles_pedido = htmlspecialchars($_POST['detalles']);

    // ===================================================
    // CONFIGURACIÓN: Coloca aquí tu correo de Gmail real
    // ===================================================
    $email_tienda = "joshuaburbano52@gmail.com"; 

    // 1. CORREO PARA LA TIENDA (Remitente)
    $asunto_tienda = "NUEVA SOLICITUD DE PEDIDO - Tienda TCG";
    $cuerpo_tienda = "Has recibido un nuevo pedido en la web.\n\n";
    $cuerpo_tienda .= "--- DATOS DEL CLIENTE ---\n";
    $cuerpo_tienda .= "Nombre: $nombre\n";
    $cuerpo_tienda .= "Correo de contacto: $email_cliente\n\n";
    $cuerpo_tienda .= "--- DETALLES DEL PEDIDO ---\n";
    $cuerpo_tienda .= "$detalles_pedido\n";
    
    $headers_tienda = "From: webmaster@tiendatcg.com\r\n";
    $headers_tienda .= "Reply-To: $email_cliente\r\n";

    // 2. CORREO DE CONFIRMACIÓN PARA EL CLIENTE (Destinatario)
    $asunto_cliente = "Confirmación de Recibo - Tu Pedido TCG Master";
    $cuerpo_cliente = "Hola $nombre,\n\nHemos recibido correctamente tu solicitud de pedido:\n\n";
    $cuerpo_cliente .= "\"$detalles_pedido\"\n\n";
    $cuerpo_cliente .= "Actualmente estamos verificando la disponibilidad del stock. Nos pondremos en contacto contigo a este correo a la brevedad para indicarte los métodos de pago y el envío.\n\n";
    $cuerpo_cliente .= "¡Gracias por tu preferencia!\nAtentamente,\nEl equipo de Tienda TCG Master.";
    
    $headers_cliente = "From: $email_tienda\r\n";

    // ENVÍO DE CORREOS (Se usa @ para mitigar errores visuales si sendmail no está activo)
    $envio_tienda = @mail($email_tienda, $asunto_tienda, $cuerpo_tienda, $headers_tienda);
    $envio_cliente = @mail($email_cliente, $asunto_cliente, $cuerpo_cliente, $headers_cliente);

    if ($envio_tienda && $envio_cliente) {
        $mensaje_alerta = "<div class='alerta exito'>¡Pedido enviado con éxito! Se ha notificado a la tienda y te hemos enviado un correo de confirmación.</div>";
    } else {
        $mensaje_alerta = "<div class='alerta error'>Error al procesar el envío. Por favor, asegúrate de tener configurado sendmail en tu panel de XAMPP.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda TCG Master - Pokémon & Yu-Gi-Oh!</title>
    <style>
        /* Estilos Base y Reset */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; color: #333; }
        
        /* Encabezado */
        header { background-color: #1e1e24; color: #ffffff; text-align: center; padding: 30px 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        header h1 { font-size: 2.3rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        header p { color: #aaa; font-size: 1rem; }

        /* Contenedor Principal */
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .seccion-titulo { text-align: center; font-size: 1.8rem; margin-bottom: 25px; color: #222; position: relative; }
        
        /* Catálogo en Grid Responsivo */
        .grid-catalogo { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px; margin-bottom: 5px; }
        
        /* Tarjetas de Producto */
        .card { background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); text-align: center; transition: transform 0.3s; display: flex; flex-direction: column; justify-content: space-between; }
        .card:hover { transform: translateY(-5px); }
        
        /* Banners de Franquicia */
        .badge { padding: 8px 0; font-weight: bold; font-size: 0.9rem; text-transform: uppercase; color: #fff; letter-spacing: 1px; }
        .badge.pokemon { background-color: #e3350d; } /* Rojo Pokémon */
        .badge.yugioh { background-color: #0d47a1; }  /* Azul Yu-Gi-Oh! */
        
        /* Contenedor de Imagen */
        .img-container { padding: 15px; background: #fafafa; display: flex; justify-content: center; align-items: center; height: 240px; }
        .img-container img { max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 4px; }
        
        /* Detalles de la Carta */
        .info { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .info h3 { font-size: 1.1rem; margin-bottom: 8px; color: #111; }
        .info p { font-size: 0.9rem; color: #666; margin-bottom: 12px; line-height: 1.4; }
        .precio { font-size: 1.3rem; font-weight: bold; color: #2e7d32; }

        /* Formulario Inferior */
        .seccion-pedido { background: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 40px; }
        .seccion-pedido h2 { text-align: center; margin-bottom: 10px; font-size: 1.6rem; }
        .seccion-pedido p { text-align: center; color: #666; margin-bottom: 25px; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
        
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: 600; margin-bottom: 8px; font-size: 0.95rem; }
        .form-group input, .form-group textarea { padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit; font-size: 1rem; transition: border 0.3s; }
        .form-group input:focus, .form-group textarea:focus { border-color: #0d47a1; outline: none; }
        
        button { background-color: #2e7d32; color: #fff; border: none; padding: 14px 20px; font-size: 1.1rem; font-weight: bold; border-radius: 5px; cursor: pointer; transition: background 0.3s; width: 100%; text-transform: uppercase; }
        button:hover { background-color: #1b5e20; }

        /* Alertas de Envío */
        .alerta { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold; font-size: 1rem; }
        .exito { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Footer */
        footer { background-color: #1e1e24; color: #888; text-align: center; padding: 20px; margin-top: 5px; font-size: 0.9rem; }
    </style>
</head>
<body>

    <header>
        <h1>Tienda TCG Master</h1>
        <p>Tu rincón exclusivo para cartas de Pokémon y Yu-Gi-Oh!</p>
    </header>

    <div class="container">
        <h2 class="seccion-titulo">Catálogo de Productos Destacados</h2>
        
        <div class="grid-catalogo">
            
            <div class="card">
                <div class="badge pokemon">Pokémon TCG</div>
                <div class="img-container">
                    <img src="https://assets.pokemon.com/assets/cms2/img/pokedex/full/006.png" alt="Charizard Base Set">
                </div>
                <div class="info">
                    <h3>Charizard Base Set 1st Ed</h3>
                    <p>Holográfica clásica, condición impecable certificada PSA 10. Una joya histórica.</p>
                    <div class="precio">$150.00</div>
                </div>
            </div>

            <div class="card">
                <div class="badge yugioh">Yu-Gi-Oh!</div>
                <div class="img-container">
                    <img src="https://images.ygoprodeck.com/images/cards/46986414.jpg" alt="Mago Oscuro">
                </div>
                <div class="info">
                    <h3>Mago Oscuro LOB 1st Ed</h3>
                    <p>El hechicero definitivo en ataque y defensa. Edición Legend of Blue Eyes PSA 9.</p>
                    <div class="precio">$80.00</div>
                </div>
            </div>

            <div class="card">
                <div class="badge pokemon">Pokémon TCG</div>
                <div class="img-container">
                    <img src="https://assets.pokemon.com/assets/cms2/img/pokedex/full/025.png" alt="Pikachu Illustrator">
                </div>
                <div class="info">
                    <h3>Pikachu Illustrator (Réplica)</h3>
                    <p>Réplica exacta de alta calidad para coleccionistas de la carta más rara del mundo.</p>
                    <div class="precio">$25.00</div>
                </div>
            </div>

            <div class="card">
                <div class="badge yugioh">Yu-Gi-Oh!</div>
                <div class="img-container">
                    <img src="https://images.ygoprodeck.com/images/cards/89631139.jpg" alt="Dragon Blanco de Ojos Azules">
                </div>
                <div class="info">
                    <h3>Blue-Eyes White Dragon</h3>
                    <p>Una fuerza poderosa de destrucción virtualmente invencible. Versión clásica.</p>
                    <div class="precio">$95.00</div>
                </div>
            </div>

        </div>

        <div class="seccion-pedido" id="pedido">
            <h2>Realizar Pedido de Cartas y Contacto</h2>
            <p>Escribe tus datos y los artículos que te interesan para enviarte la orden de compra inmediata.</p>
            
            <?= $mensaje_alerta ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>#pedido" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo:</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Pérez" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Tu Correo Electrónico:</label>
                        <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="detalles">Detalles del Pedido / Mensaje:</label>
                    <textarea id="detalles" name="detalles" rows="5" placeholder="Ej: Hola, deseo comprar 1x Charizard Base Set y saber si tienen stock de sobres de Yu-Gi-Oh!..." required></textarea>
                </div>

                <button type="submit">Enviar Pedido y Recibir Confirmación</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Tienda TCG Master. Todos los derechos reservados.</p>
    </footer>

</body>
</html>