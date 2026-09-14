<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\Json;
use common\models\Profilo;
use UUID\UUID;
use common\models\scontrino\Scontrino as Informazioni;

/**
 * This is the model class for table "scontrino".
 *
 * @property int $id
 * @property int|null $profilo_id
 * @property string|null $content
 * @property string|null $creato_il
 * @property string|null $modificato_il
 *
 * @property Profilo $profilo
 */
class Scontrino extends \yii\db\ActiveRecord
{
    public $imageFile;

    public $uuid;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scontrino';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['profilo_id'], 'integer'],
            [['content', 'filename', 'totale', 'ragione_sociale', 'partita_iva', 'indirizzo', 'data_doc', 'ora_doc'], 'string'],
            [['creato_il', 'modificato_il'], 'safe'],
            [['profilo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profilo::class, 'targetAttribute' => ['profilo_id' => 'id']],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    public function getResized($w = 480, $h = 480, $crop=FALSE) {
        if (!$this->filename) {
            return null;
        }

        if ($this->base64resized) {
            return $this->base64resized;
        }

        list($width, $height) = getimagesize($this->filename);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($this->filename);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    
        ob_start (); 

        imagejpeg ($dst);
        $image_data = ob_get_contents (); 

        ob_end_clean (); 

        $image_data_base64 = base64_encode($image_data);

        $this->base64resized = $image_data_base64;
        $this->save(false);

        return $image_data_base64;
    }

    public function getItems()
    {
        if (!$this->articoli) {
            return [];
        } else {
            return Json::decode($this->articoli);
        }
        return [];
    }

    public function analyze()
    {
        if ($this->content) {
            $content = Json::decode($this->content);
            if (is_array($content) && isset($content['receipts']) && is_array($content['receipts']) && !empty($content['receipts'])) {
                $scontrino = $content['receipts'][0];

                $this->ragione_sociale = (isset($scontrino['merchant_name']) && $scontrino['merchant_name'] && !$this->ragione_sociale) ? $scontrino['merchant_name'] : $this->ragione_sociale;
                $this->partita_iva = (isset($scontrino['merchant_tax_reg_no']) && $scontrino['merchant_tax_reg_no'] && !$this->partita_iva) ? $scontrino['merchant_tax_reg_no'] : $this->partita_iva;

                // Cerca la partita iva dentro il DB delle aziende aderenti
                if ($this->partita_iva) {
                    $esercente = Profilo::find()->where(['=', 'partita_iva', $this->partita_iva])->one();
                    if ($esercente) {
                        $this->esercente_id = $esercente->id;
                    }
                }
                
                if (isset($scontrino['items']) && is_array($scontrino['items']) && !empty($scontrino['items'])) {
                    $this->articoli = Json::encode($scontrino['items']);
                }

                $this->totale = (isset($scontrino['total']) && !$this->totale) ? $scontrino['total'] : $this->totale;

                $this->indirizzo = (isset($scontrino['merchant_address']) && !$this->indirizzo) ? $scontrino['merchant_address'] : $this->indirizzo;
                $this->data_doc = (isset($scontrino['date']) && !$this->data_doc) ? $scontrino['date'] : $this->data_doc;
                $this->ora_doc = (isset($scontrino['time']) && !$this->ora_doc) ? $scontrino['time'] : $this->ora_doc;

                return true;
            }
        }
        return false;
    }

    public function fileExists()
    {
        return file_exists($this->filename);
    }

    public function getFileEncode()
    {
        $fileEncode = base64_encode(file_get_contents($this->filename));
        return $fileEncode;
    }

    public function getFileData()
    {
        $fileData = exif_read_data($this->filename);
        return $fileData;
    }

    public function getMimeType()
    {
        return $this->fileData['MimeType'];
    }

    public function upload()
    {
        $this->uuid = UUID::uuid7();

        if ($this->validate() && !is_null($this->imageFile)) {
            /* Vecchia implementazione
            $fileparams = [];
            $filename = $this->imageFile->baseName;
            $mimetype = $this->imageFile->type;
            $size = $this->imageFile->size;
            $extension = $this->imageFile->extension;
            $tmpfilename = $this->imageFile->tempName;
            $upload_date = date('d-m-Y H:i:s');
            $hashfilename = hash('sha256', $filename . time());
            $this->imageFile->saveAs($base . $hashfilename . '.' . $extension);
            $fileparams = ['filename' => $filename, 'tempName' => $tmpfilename, 'mimetype' => $mimetype, 'size' => $size, 'extension' => $extension, 'hashfilename' => $hashfilename];
            $logcontent = "Upload \n ============== \n\n nomefile: ". $filename. "\n nometemporaneo: ". $tmpfilename ."\n tipo file: ". $mimetype . "\n dimensione: " . $size . "\n estensione: " . $extension . "\n hashnomefile: ". $hashfilename . "\n data upload: ". $upload_date ."\n\n =================\n";
            $logger->logUpload($logcontent);
            return $fileparams;
            */
            $this->filename = Yii::getAlias('@runtime') . '/uploads/' . $this->uuid . '.' . $this->imageFile->extension;
            $this->imageFile->saveAs($this->filename);

            // Copy the file into another location if needed
            $copyPath = Yii::getAlias('@runtime') . '/../../../receipts/inbox/' . $this->uuid . '.' . $this->imageFile->extension;
            copy($this->filename, $copyPath);

            return true;
        } else if (is_null($this->imageFile) && $this->id) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'profilo_id' => 'Profilo ID',
            'content' => 'Content',
            'creato_il' => 'Creato Il',
            'modificato_il' => 'Modificato Il',
        ];
    }

    /**
     * Gets query for [[Profilo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfilo()
    {
        return $this->hasOne(Profilo::class, ['id' => 'profilo_id']);
    }

    /**
     * Gets query for [[Profilo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEsercente()
    {
        return $this->hasOne(Profilo::class, ['id' => 'esercente_id']);
    }

    /**
     * Gets query for [[Coupon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCoupon()
    {
        return $this->hasOne(Coupon::class, ['scontrino_id' => 'id']);
    }

    public function execOcrAnalyze()
    {
        if (!$this->filename) {
            return false;
        }
        $this->content = \Yii::$app->ocr->getContent($this->filename);
        $this->save(false);
        $result_analyze = $this->analyze();
        $this->valido = $this->content && $result_analyze;
        $this->save(false);

        return $this->valido;
    }

    public function beforeSave($insert){
        if (!$this->sid) {
            $this->sid = $this->uuid;
        }
        return parent::beforeSave($insert);
    }    

    public function getInformazioni()
    {
        return $this->hasMany(Informazioni::class, ['receipt_id' => 'sid']);
    }
}
