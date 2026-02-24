<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Page;

class PageDisplay extends Component
{
    public $page;
    public $processedContent;
    public $hasContactForm = false;
    
    /**
     * Supported shortcodes and their replacements
     */
    protected $shortcodes = [
        '[contact-form]' => 'contact-form',
        '[contact_form]' => 'contact-form',
        '{{contact-form}}' => 'contact-form',
    ];
     
    public function mount($slug)
    {
        $this->page = Page::where('slug', $slug)->firstOrFail();
        $this->processContent();
    }
    
    /**
     * Process content and detect shortcodes
     */
    protected function processContent()
    {
        $content = $this->page->content;
        
        // Check for contact form shortcode
        foreach ($this->shortcodes as $shortcode => $type) {
            if (stripos($content, $shortcode) !== false) {
                if ($type === 'contact-form') {
                    $this->hasContactForm = true;
                    // Remove the shortcode from content - we'll render the form separately
                    $content = str_ireplace($shortcode, '', $content);
                }
            }
        }
        
        $this->processedContent = trim($content);
    }

    public function render()
    {
        return view('livewire.page-display');
    }

    public function showAbout()
    {
        return redirect()->to('/about');
    }
}