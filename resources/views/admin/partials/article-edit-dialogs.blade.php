{{--
    The "Modifier l'actualité" dialogs, one per article.

    Two screens render them: the actualités listing, and the dashboard's
    "Dernières actualités" panel. Before this partial only the listing
    had them, so the dashboard's Modifier had to send the administrator
    to the listing with ?ouvrir= just to reach a dialog — they pressed
    Modifier on the dashboard and arrived on another screen, and closing
    the dialog left them there rather than where they had been working.

    The markup lives here once: a field added to the form appears on both
    screens, and the two cannot drift apart.

    Callers pass $articles (the rows to build a dialog for) and
    $categories (the select's options).

    A dialog is rendered outside the table, never between tr elements:
    a dialog is not valid content there, and a browser recovering from
    that parse foster-parents it out of the table, silently corrupting
    the form markup inside without ever raising a console error.
--}}
            @foreach ($articles as $article)
                <x-admin.edit-modal id="edit-article-{{ $article->id }}" title="Modifier l'actualité" :action="route('admin.news.update', $article)">
                    <div>
                        <label for="edit-article-{{ $article->id }}-title" class="block text-sm font-medium text-slate-700">
                            Titre
                            <span class="text-rose-600" aria-hidden="true">*</span>
                        </label>
                        <input type="text" id="edit-article-{{ $article->id }}-title" name="title" required value="{{ $article->title }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label for="edit-article-{{ $article->id }}-category" class="block text-sm font-medium text-slate-700">Catégorie</label>
                        <select id="edit-article-{{ $article->id }}-category" name="category_id" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected($article->category_id === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit-article-{{ $article->id }}-excerpt" class="block text-sm font-medium text-slate-700">Chapô</label>
                        <textarea id="edit-article-{{ $article->id }}-excerpt" name="excerpt" rows="2" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $article->excerpt }}</textarea>
                    </div>

                    <div>
                        <label for="edit-article-{{ $article->id }}-body" class="block text-sm font-medium text-slate-700">Contenu</label>
                        <textarea id="edit-article-{{ $article->id }}-body" name="body" rows="6" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">{{ $article->body }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="edit-article-{{ $article->id }}-status" class="block text-sm font-medium text-slate-700">Statut</label>
                            <select id="edit-article-{{ $article->id }}-status" name="status" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                <option value="draft" @selected($article->status === 'draft')>Brouillon</option>
                                <option value="published" @selected($article->status === 'published')>Publié</option>
                                <option value="archived" @selected($article->status === 'archived')>Archivé</option>
                            </select>
                        </div>

                        <div>
                            <label for="edit-article-{{ $article->id }}-published-at" class="block text-sm font-medium text-slate-700">Date de publication</label>
                            <input type="date" id="edit-article-{{ $article->id }}-published-at" name="published_at" value="{{ $article->published_at?->format('Y-m-d') }}" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                        </div>
                    </div>

                    <x-admin.image-field
                        name="image"
                        :id="'edit-article-'.$article->id.'-image'"
                        label="Image à la une"
                        :current="$article->image"
                        :preview="$article->imageUrl()"
                        accept="image/jpeg,image/png"
                    />
                </x-admin.edit-modal>
            @endforeach
