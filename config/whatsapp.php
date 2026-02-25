<?php
/**
 * Configuración de WhatsApp Business API
 */

// Número de teléfono del remitente (con código de país)
define('WHATSAPP_PHONE_FROM', '573112969569'); // Tu número de WhatsApp Business

// Token de acceso (si usas API oficial)
define('WHATSAPP_API_TOKEN', ''); // Dejar vacío si no tienes API configurada

// URL base de la API (si usas API oficial)
define('WHATSAPP_API_URL', 'https://graph.instagram.com/v18.0/');

// Número de teléfono ID (si usas API oficial)
define('WHATSAPP_PHONE_ID', '');

// Si no tienes API configurada, usar enlaces de WhatsApp Web
define('WHATSAPP_USE_WEB_LINK', true);
