# StockMaster
Nombre y descripción del proyecto
StockMaster
El objetivo general del proyecto es desarrollar e implementar un sistema de
información web integral, denominado "STOCKMASTER", diseñado para
modernizar y automatizar la gestión de inventarios de un almacén. 
Integrantes:
BERMUDEZ TAPIA HECTOR ANTONIO
NAVARRO DE LA CRUZ JONATHAN
PATRICIO
SANCHEZ ROMERO CHRISTIAN
VILCHIS EZPINOZA ALFREDO
GONZALEZ HERNANDEZ JORGE URIEL 
Tecnologías utilizadas:
HTML, CSS, PHP, SQL, JAVA SCRIPT
Arquitectura y microservicios
INICIO (login.php)
│
├── [Administrador] panel_admin.php
│   ├── usuarios.php
│   │   ├── nuevo_usuario.php
│   │   └── editar_usuario.php
│   ├── proveedores.php
│   │   └── nuevo_proveedor.php
│   ├── productos.php
│   │   └── nuevo_producto.php
│   ├── inventario.php
│   │   └── detalle_inventario.php
│   ├── reportes.php
│   │   ├── reporte_entradas.php
│   │   ├── reporte_salidas.php
│   │   └── reporte_inventario.php
│   └── configuración (cambiar_contraseña.php, logout.php)
│
├── [Operario de almacén] panel_operario.php
│   ├── entrada_mercancia.php
│   ├── salida_mercancia.php
│   ├── verificar_mercancia.php
│   └── detalle_entrada.php / detalle_salida.php
│
├── [Gerente de almacén] panel_gerente.php
│   ├── autorizar_entrada.php
│   ├── autorizar_salida.php
│   ├── supervisar_inventario.php
│   └── reportes.php
│
└── [Cliente] panel_cliente.php
    ├── perfil_cliente.php
    ├── pedidos.php
    │   ├── nuevo_pedido.php
    │   └── detalle_pedido.php
    └── logout.php
