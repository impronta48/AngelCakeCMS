<?php
namespace App\Model;

use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Core\Configure;
use Cake\Utility\Text;


class StaticModel
{
  private $staticImgPath;

  public function __construct()
  {
        //Imposto la cartella dove si trovano le immagini statiche
        $sitedir = Configure::read('sitedir');
        $this->staticImgPath = "/$sitedir/static/img/";  
  }

  public function findAll()
  {
        $sitedir = Configure::read('sitedir');
        $name = $sitedir . DS . 'static' . DS ;        
        
        //Caricare il nostro frontmatter in modo che legga tutti i file nella cartella static 
        $dir = new Folder($name) ;
        $t1 = microtime(TRUE);
        $files = $dir->findRecursive('.*\.md');
        
        $risult = [];
        foreach ($files as $k => $f) {
            //Ignoro i file che iniziano con underscore
            if ($this->file_begins_with_underscore($f))
            {
                continue;
            }

            $risult[$k]['file']  =$f;
            $risult[$k]['dati'] = $this->leggi_file_md($f);
            
            //Se nel file md metto sitemap=false questa pagina non finisce nella sitemap
            if (isset($risult[$k]['dati']['sitemap']) && $risult[$k]['dati']['sitemap'] == false)
            {
                unset($risult[$k]);
                continue;
            }  
            
            if (!isset($risult[$k]['dati']['date'])) {
                $risult[$k]['dati']['date'] = null;
            }
            unset($risult[$k]['dati']['body']);

        }
        
        //Ordino l'array dei risultati per il campo date invertito
        usort($risult, function ($a, $b) { return -1*strcmp($a['dati']['date'], $b['dati']['date']); });
        //$t2 = microtime(TRUE);
        //dd($t2-$t1);

        //dd($risult);
        return $risult;
    }

    private function file_begins_with_underscore($f)
    {
        $bname= basename($f);
        $dname = dirname($f);
        $parts = explode('/',$dname);
        foreach ($parts as $p) {
            if (strlen($p) > 0 and $p[0]=='_'){
                return true;
            }
        }
        return ($bname[0]=='_');
    }

    public function combina_path(...$path){
        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }

        return implode('/', $path);
    }

    public function leggi_file_md($fname)
    {        
        //Visualizza la pagina che si chiama $name.md
        $parser = new \hkod\frontmatter\Parser(
        new \hkod\frontmatter\YamlParser,
        new \hkod\frontmatter\MarkdownParser
        );

        $dati =[];
        try{
        $file = new File($fname);
                
        $path_parts = pathinfo($fname);
        $bname = $path_parts['basename'];
        $fname = $path_parts['filename'];
        $path = $path_parts['dirname'];
        $miniPath = str_replace(WWW_ROOT . Configure::read('sitedir') . '/static', '', $path);
        $miniPath = str_replace(Configure::read('sitedir') . '/static', '', $miniPath);

        $contents = $file->read();
        $result = $parser->parse($contents);
        $body = $result->getBody();
        $variabili = $result->getFrontmatter();
        
        /*DESCRIPTION*/
        if(!isset($variabili['description']))
        {
            $description= Text::truncate(
                strip_tags($body),
                200,
                ['ellipsis' => '...']
            );
            //dd($description);
            $variabili['description']= $description;
        }

        /*TITLE */
        if(!isset($variabili['title']))
        {
            $title=str_replace('-', ' ', $fname);
            $variabili['title']= $title;
        }

        /*CANONICAL*/
        if(!isset($variabili['canonical']))
        {
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'http://' : 'https://';
            $canonical= $protocol . env('HTTP_HOST') . DS . 'static' . DS . 'view' . $miniPath . DS . $fname;
            $variabili['canonical']= $canonical;
        }

        /*COPERTINA*/
        if(isset($variabili['copertina']))            
        {        
            //Se la copertina inizia con http non faccio nulla   
            if (strpos($variabili['copertina'],'http') !== 0 )
            {   
                $variabili['copertina']= $this->staticImgPath . $variabili['copertina'] ;    
            }
            
        }
        $variabili['body']= $body;
        
        $file->close();
        return $variabili;
    } 
    catch (MissingTemplateException $exception) {
        if (Configure::read('debug')) {
            throw $exception;
        }
        throw new NotFoundException();
    }
    }



    //Toglie il basepath da un path complessivo
    private function relativePath($base, $full)
    {
        return trim(str_replace($base, '', $full));
    }

}