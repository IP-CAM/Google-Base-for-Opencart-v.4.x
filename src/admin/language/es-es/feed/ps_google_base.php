<?php
// Heading
$_['heading_title']                = 'Playful Sparkle - Google Base';
$_['heading_categories']           = 'Asociaciones de Categorías';
$_['heading_authentication']       = 'Autenticación';
$_['heading_tax_deffinitions']     = 'Definiciones de Impuestos';
$_['heading_getting_started']      = 'Comenzando';
$_['heading_setup']                = 'Configuración de Google Base';
$_['heading_troubleshot']          = 'Solución de Problemas Comunes';
$_['heading_faq']                  = 'Preguntas Frecuentes';
$_['heading_contact']              = 'Contacto de Soporte';

// Text
$_['text_extension']               = 'Extensiones';
$_['text_success']                 = 'Éxito: ¡Has modificado el feed de Google Base!';
$_['text_import_success']          = 'Éxito: ¡Has importado correctamente la lista de categorías de Google!';
$_['text_add_category_success']    = 'Éxito: ¡Has añadido correctamente la asociación de categorías!';
$_['text_remove_category_success'] = 'Éxito: ¡Has eliminado correctamente la asociación de categorías!';
$_['text_edit']                    = 'Editar Google Base';
$_['text_import']                  = 'Para descargar la lista más reciente de categorías de Google, <a href="https://support.google.com/merchants/answer/160081?hl=en" target="_blank" rel="external noopener noreferrer" class="alert-link">haz clic aquí</a> y selecciona la taxonomía con IDs numéricos en un archivo de texto plano (.txt). Luego, sube el archivo descargado usando el botón verde.';
$_['text_getting_started']         = '<p><strong>Visión General:</strong> El Google Base para OpenCart 4.x permite a los usuarios exportar sin esfuerzo sus productos al <a href="https://merchants.google.com" target="_blank" rel="external noopener noreferrer">Google Merchant Center</a> en formato XML. Esta herramienta esencial mejora la visibilidad de los productos en Google Shopping, facilitando la descubribilidad y compra por parte de los clientes potenciales.</p><p><strong>Requisitos:</strong> Para utilizar esta extensión, asegúrate de tener instalado y configurado OpenCart 4.x+, PHP 7.4+ y una cuenta activa de Google Merchant Center para gestionar tus listas de productos de manera efectiva.</p>';
$_['text_setup']                   = '<ul><li>Descarga la lista más reciente de categorías de Google desde <a href="https://support.google.com/merchants/answer/160081?hl=en" target="_blank" rel="external noopener noreferrer">Soporte de Google Merchant Center</a>.</li><li>Asocia las categorías de tu tienda con las categorías de Google correspondientes.</li><li>Configura la extensión para omitir productos fuera de stock si lo deseas.</li><li>Utiliza precios antes de impuestos en el feed activando la opción respectiva y personaliza las definiciones de impuestos según sea necesario.</li></ul>';
$_['text_troubleshot']             = '<ul><li><strong>Categorías de Google no mostradas:</strong> Asegúrate de haber descargado la lista más reciente de categorías de Google e importado al sistema. Esto es esencial para el mapeo adecuado entre tu tienda y Google Merchant Center.</li><li><strong>No hay salida del feed de productos:</strong> Confirma que la extensión de Google Base esté habilitada en el panel de administración de OpenCart. Si está habilitada y aún no ves ninguna salida, revisa la configuración de la extensión por posibles errores.</li><li><strong>Productos no aparecen en Google Merchant Center:</strong> Verifica que tus productos estén correctamente categorizados y que no haya errores en los datos del producto. Asegúrate de cumplir con las políticas de Google Merchant Center sobre listas de productos.</li></ul>';
$_['text_faq']                     = '<details><summary>¿Qué es la extensión de Google Base?</summary><p>La extensión de Google Base ayuda a los usuarios de OpenCart 4.x a exportar sus datos de productos a Google Merchant Center, mejorando la visibilidad en Google Shopping.</p></details><details><summary>¿Cómo habilito la extensión de Google Base?</summary><p>Puedes habilitar la extensión desde el panel de administración de OpenCart en la sección de Extensiones. Asegúrate de configurar los ajustes según sea necesario.</p></details><details><summary>¿Puedo personalizar mi feed de productos?</summary><p>Sí, la extensión permite personalizar varios ajustes, incluyendo el uso de precios antes de impuestos y definiciones de impuestos, asegurando que tu feed cumpla con tus necesidades.</p></details><details><summary>¿Por qué no aparece mi feed de productos en Google Merchant Center?</summary><p>Asegúrate de que la extensión de Google Base esté habilitada y que tus categorías de productos estén correctamente asociadas con las categorías de Google. Además, revisa si hay errores en la configuración de tu feed.</p></details>';
$_['text_contact']                 = '<p>Para más asistencia, por favor contacta a nuestro equipo de soporte:</p><ul><li><strong>Contacto:</strong> <a href="mailto:%s">%s</a></li><li><strong>Documentación:</strong> <a href="%s" target="_blank" rel="noopener noreferrer">Documentación para el usuario</a></li></ul>';
$_['text_gbc2c_restore']           = 'El proceso de restauración es destructivo y eliminará las asociaciones de categorías actuales para la tienda seleccionada. Asegúrese de haber seleccionado la tienda correcta para importar y que el archivo de respaldo que suba (gbc2c_backup_store_x.txt) termine con el ID de la tienda (x) en la que desea restaurar.';

// Column
$_['column_google_category']       = 'Categoría de Google';
$_['column_category']              = 'Categoría';
$_['column_action']                = 'Acción';

// Tab
$_['tab_general']                  = 'General';
$_['tab_help_and_support']         = 'Ayuda y Soporte';

// Entry
$_['entry_google_category']        = 'Categoría de Google';
$_['entry_category']               = 'Categoría';
$_['entry_data_feed_url']          = 'URL del Feed de Datos';
$_['entry_status']                 = 'Estado';
$_['entry_login']                  = 'Nombre de Usuario';
$_['entry_password']               = 'Contraseña';
$_['entry_skip_out_of_stock']      = 'Omitir Productos Fuera de Stock';
$_['entry_tax']                    = 'Usar precios antes de impuestos';
$_['entry_country']                = 'País';
$_['entry_region']                 = 'Región';
$_['entry_tax_rate']               = 'Tasa de Impuesto';
$_['entry_tax_ship']               = 'Impuesto de Envío';
$_['entry_active_store']           = 'Tienda activa';
$_['entry_additional_images']      = 'Incluir imágenes adicionales';
$_['entry_backup_restore']         = 'Respaldar/Restaurar';

// Button
$_['button_backup']                = 'Hacer copia de seguridad';
$_['button_restore']               = 'Restaurar';

// Help
$_['help_copy']                    = 'Copiar URL';
$_['help_open']                    = 'Abrir URL';
$_['help_additional_images']       = 'Activar esta opción añadirá imágenes adicionales a su feed de Google Base. Tenga en cuenta que habilitar esta opción puede ralentizar el proceso de generación del feed y aumentar el tamaño del archivo XML generado.';

// Error
$_['error_permission']             = 'Advertencia: ¡No tienes permiso para modificar el feed de Google Base!';
$_['error_store_id']               = 'Advertencia: ¡El formulario no contiene store_id!';
$_['error_currency']               = 'Advertencia: Selecciona una moneda de la lista';
$_['error_upload']                 = '¡El archivo no se pudo subir!';
$_['error_filetype']               = '¡Tipo de archivo no válido!';
$_['error_tax_country']            = 'Por favor, selecciona un país para el impuesto.';
$_['error_tax_region']             = 'El campo de región del impuesto no puede quedar vacío.';
$_['error_tax_rate_id']            = 'Por favor, selecciona una tasa de impuesto para el impuesto';
$_['error_no_data_to_backup']      = 'No hay datos de asociaciones de categorías disponibles para hacer una copia de seguridad.';
