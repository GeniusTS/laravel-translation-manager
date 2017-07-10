<?php

namespace GeniusTS\TranslationManager\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use GeniusTS\TranslationManager\Manager;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use GeniusTS\TranslationManager\Requests\TranslationRequest;

/**
 * Class Controller
 *
 * @package GeniusTS\TranslationManager
 */
class Controller extends BaseController
{

    use ValidatesRequests;

    /**
     * @var \GeniusTS\TranslationManager\Manager
     */
    protected $manager;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->manager = new Manager;
    }

    /**
     * Display index page of translation
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $namespaces = $this->manager->namespaces();

        return view('translation_manager::index', compact('namespaces'));
    }

    /**
     * Display edit form page
     *
     * @param string      $language
     * @param string      $file
     * @param string|null $namespace
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($language, $file, $namespace = null)
    {
        $translations = $this->manager->translations($file, $namespace, $language);
        $prefix = ($namespace ? "{$namespace}::" : "") . "{$this->manager->groupName($file)}.";

        return view(
            'translation_manager::edit',
            compact('language', 'file', 'namespace', 'translations', 'prefix')
        );
    }

    /**
     * Save translation file
     *
     * @param \GeniusTS\TranslationManager\Requests\TranslationRequest $request
     * @param string                                                   $language
     * @param string                                                   $file
     * @param string|null                                              $namespace
     *
     * @return \Illuminate\Http\Response
     */
    public function update(TranslationRequest $request, $language, $file, $namespace = null)
    {
        $keys = array_keys($this->manager->translations($file, $namespace));

        $this->manager->exportFile($request->only($keys), $file, $language, $namespace);

        return redirect()
            ->route('translation_manager.index')
            ->with('message', 'Translation added successfully');
    }

    /**
     * get array of namespace's files
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function files(Request $request)
    {
        $this->validate($request, ['namespace' => 'nullable|string']);

        return new JsonResponse($this->manager->files($request->get('namespace')));
    }
}