<?php

namespace Backend\Root\Sitemap\Controllers;

use App\Http\Controllers\Controller;
use Backend\Root\Core\Services\Helpers;
use Response;
use Content;
use BackendConfig;

class SitemapController extends Controller
{


    private function setData(&$cats, &$parentEl = ['id' => 0] ){
        foreach ($cats as &$cat) {
            if($cat['parent_id'] == $parentEl['id']){
                if($cat['parent_id'] == 0){
                    if(!isset($cat['enable']))$cat['enable'] = '1';
                    if(!isset($cat['link-enable']))$cat['link-enable'] = '1';
                    if(!isset($cat['changefreq']))$cat['changefreq'] = '';
                    if(!isset($cat['priority']))$cat['priority'] = '';
                } else {
                    if(!isset($cat['enable']) || $parentEl['enable'] == '0'){
                        $cat['enable'] = $parentEl['enable'];
                    }
                    if(!isset($cat['link-enable'])){
                        $cat['link-enable'] = $parentEl['link-enable'];
                    }
                    if(!isset($cat['changefreq']) || $cat['changefreq'] == ''){
                        $cat['changefreq'] = $parentEl['changefreq'];
                    }
                    if(!isset($cat['priority']) || $cat['priority'] == ''){
                        $cat['priority'] = $parentEl['priority'];  
                    }
                }
                $this->setData($cats, $cat);
            }
        }
    }

    public function index($url = '')
    {
        $urls = []; 
        if($url != ''){
            //Get all categories with sitemap pref...
            $url = ucfirst($url);

            $allCats = [];
            foreach (\Backend\Root\Category\Models\Category::where('mod', $url)->get(['id', 'mod', 'parent_id', 'array_data', 'updated_at', 'url']) as $cat) {       
                $allCats[$cat['id']] = Helpers::setArray($cat, ['id', 'parent_id', 'mod','url']);

                foreach ($cat['array_data']['fields'] as $key => $el) {
                    if(preg_match("/^sitemap-(.*)/", $key, $matches)){
                        $allCats[$cat['id']][$matches[1]] = $el;
                    }
                }
                $allCats[$cat['id']]['date'] = date("c", strtotime($cat['updated_at']));
            }

            if(count($allCats) == 0)abort(404); 
            
            $this->setData($allCats);

            //Print categories with link-enable
            foreach ($allCats as $cat) {
                if($cat['enable'] && $cat['link-enable']){
                    $cat['url'] = Content::getUrl($cat);
                    $urls[] = $cat;
                }
            }
            
            //Get models data
            $cont = '\Backend\Root\\'.$url.'\Models\\'.$url;
            $cont::chunk(200, function ($data) use (&$allCats, &$urls) {
                foreach ($data as $el) {
                    if(!isset($allCats[$el['category_id']]) || $allCats[$el['category_id']]['enable'] == '0'){
                        continue;
                    }
                    $elEnable = Helpers::dataIsSet($el, 'sitemap-enable');
                    if($elEnable === '0') continue;

                    if( ($elUrl = Helpers::dataIsSet($el, 'sitemap-url') ))
                        $item['url'] = url($elUrl);
                    else 
                        $item['url'] = Content::getUrl($el);
                
                    $item['date'] = date("c", strtotime($el['updated_at']));

                    if( ($changefreq = Helpers::dataIsSet($el, 'sitemap-changefreq') ) )
                        $item['changefreq'] = $changefreq;
                    else 
                        $item['changefreq'] = $allCats[$el['category_id']]['changefreq'];
                    
                    if( ($changefreq = Helpers::dataIsSet($el, 'sitemap-priority') ) )
                        $item['priority'] = $changefreq;
                    else 
                        $item['priority'] = $allCats[$el['category_id']]['priority'];


                    $urls[] = $item;
                    
                }
            });

            return response(view('Sitemap::urlset', [ 'data' => $urls ] ))
                    ->withHeaders(['Content-Type' => 'text/xml']);

        }elseif($url == '') {
            return response( view('Sitemap::sitemapindex', [ 'data' => array_keys(BackendConfig::get('category-modules', true)) ] ))
                   ->withHeaders(['Content-Type' => 'text/xml']);
        }
        
        abort(404);
    }

}
