<?php
class Util
{
    public static function salvarArquivo()
    {
        
        $diretorioUpload = "uploads/";

        if (!is_dir($diretorioUpload)) {
            mkdir($diretorioUpload, 0755, true);
        }

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $arquivoTmp = $_FILES['imagem']['tmp_name'];
            $nomeOriginal = basename($_FILES['imagem']['name']);
            $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

            $nomeUnico = uniqid("img_", true) . "." . $extensao;

            $caminhoFinal = $diretorioUpload . $nomeUnico;

            if (move_uploaded_file($arquivoTmp, $caminhoFinal)) {
                return $nomeUnico;
            }
        }
        return 'default_serie.jpg'; 
    }
}
?>