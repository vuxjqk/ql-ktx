<x-app-layout>
    <x-slot name="header">
        Hợp đồng
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-center uppercase font-semibold text-xl text-gray-800 leading-tight">
                Hợp đồng thuê chỗ ở nội trú
            </h1>

            <div class="mt-6">
                <p class="text-justify indent-8">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Sint, fugiat nihil, unde rerum, asperiores
                    in incidunt facilis officiis adipisci vitae numquam ipsa nemo alias accusantium. Praesentium numquam
                    perspiciatis commodi quaerat.
                    Magnam, nesciunt minus! Repellat nisi quam dolor iusto minus saepe rerum incidunt labore nam
                    tempora, unde ut repellendus beatae maiores? Maxime, beatae blanditiis non tempore obcaecati quas
                    ducimus at id?
                    Repudiandae quos minima rerum saepe, hic a soluta explicabo ipsa obcaecati dolorum recusandae vitae
                    ut necessitatibus tempore suscipit impedit sit doloribus maiores illo optio aperiam? Vel vitae
                    ratione ipsum? Sunt.
                    Molestiae ipsam provident adipisci mollitia dignissimos perspiciatis laudantium, dolor quis ab
                    itaque ut beatae totam doloremque iusto nulla eligendi esse cupiditate modi non, exercitationem,
                    repellendus quaerat unde accusamus perferendis. Reiciendis.
                    Voluptatibus accusamus, modi tempora aliquam sequi facilis quia fugiat rem pariatur possimus cumque
                    molestiae qui sed beatae, et aperiam consectetur, soluta iusto expedita ab? Voluptatum reiciendis
                    aliquam eligendi maxime doloremque?
                    Magnam doloremque fuga cupiditate odit esse quaerat enim laudantium voluptates, provident quam ipsam
                    suscipit cumque deserunt ipsum modi laborum optio. Veniam esse eveniet modi aperiam exercitationem
                    labore quia est autem.
                    Illo distinctio soluta a provident natus aliquid corrupti magnam deserunt officiis! Cupiditate id
                    exercitationem est suscipit earum. Quo id soluta, fuga doloribus facere tempore sequi officiis,
                    recusandae voluptates optio deserunt.
                    Illum quas velit blanditiis et numquam sapiente voluptatem doloremque eaque, labore eius culpa error
                    fugiat facere eveniet nam dolorem, perspiciatis id a? Cupiditate temporibus molestiae illo cum,
                    suscipit optio sint?
                    Reiciendis praesentium tenetur, accusantium corporis nam at harum voluptas magnam, architecto,
                    voluptatem laborum deserunt officiis molestiae debitis fuga explicabo quaerat commodi itaque
                    laboriosam quisquam modi error et perspiciatis. Aliquam, odit.
                    Impedit sunt, debitis ut qui voluptates necessitatibus veniam iure id officiis obcaecati porro ipsa,
                    reprehenderit ea? Optio atque numquam sunt recusandae pariatur eveniet, delectus totam natus rerum.
                    Repudiandae, esse enim?
                    Fugiat commodi minus eius voluptates est? Ut deleniti aut molestias eligendi excepturi, mollitia
                    perspiciatis natus aperiam magni deserunt ex inventore reiciendis, nisi id facilis, quas totam
                    commodi maiores porro est!
                    Eius ducimus suscipit, cumque reiciendis iusto cum explicabo corporis soluta. Omnis deleniti illum
                    laborum, voluptas aperiam officia voluptates quod nobis, eveniet ab provident culpa mollitia itaque
                    ipsum, saepe corporis! Iure?
                    Ipsum harum enim magnam voluptas, saepe inventore sit, dicta facilis officiis nobis eaque dolores
                    doloremque? Odio distinctio repudiandae, ullam tempore veniam voluptate cupiditate dolorem est
                    maiores omnis, unde officiis magni.
                    Laboriosam, dolorem quos aspernatur quod ad, nulla aut facere consectetur dignissimos pariatur quo?
                    Quidem repudiandae est eligendi esse quasi quia ut doloremque, culpa tempora. Molestiae sunt in eos
                    voluptates mollitia.
                    Nemo maiores at laboriosam magnam, ad alias a eaque deleniti delectus commodi obcaecati
                    exercitationem necessitatibus quidem repellat qui? Consequatur explicabo illum, vel reprehenderit
                    magni labore neque similique. Inventore, dicta optio.
                    Maxime enim, iusto vero repellat quae est in ullam quasi id, consequatur, neque iure. Quibusdam,
                    nobis placeat, excepturi libero ut eius fuga, eaque deleniti culpa corrupti exercitationem dicta.
                    Ea, doloremque.
                    Neque, illo fugiat nobis fugit ad cum quibusdam similique tempora distinctio reprehenderit
                    necessitatibus expedita optio deserunt nulla eligendi quaerat nisi unde dolor beatae tempore iste
                    id! Soluta cum sunt repellendus.
                    Eos neque consectetur iste beatae odit nemo omnis quod id in nam eum, fugit nobis facilis ipsum,
                    reprehenderit aliquam sint ipsa, temporibus praesentium perferendis iusto unde? Molestias beatae
                    mollitia quibusdam?
                    Reiciendis maxime dicta libero consequuntur molestiae deleniti obcaecati perspiciatis cupiditate
                    consectetur sequi ipsam odit, hic illo possimus voluptatem harum. Sapiente dolorem quasi beatae qui
                    rerum enim quas a, corporis doloribus.
                    Soluta nam corrupti esse ipsum libero labore quaerat eum quae inventore, temporibus tempora aperiam
                    architecto odit maiores tenetur, debitis voluptatum? Sint eius facere maiores tempore, neque ipsam
                    unde nesciunt eos.
                </p>
            </div>
        </div>
    </div>

    <div class="sticky bottom-0 max-w-7xl mx-auto sm:px-12 lg:px-14 bg-white py-6">
        <form action="{{ route('room_assignments.update', $roomAssignment) }}" method="post"
            class="flex items-center justify-between">
            @csrf
            @method('PUT')

            <label for="confirmation" class="inline-flex items-center">
                <input id="confirmation" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="confirmation">
                <span class="ms-2 text-sm text-gray-600">
                    Tôi chấp nhận các điều khoản và điều khiển của hợp đồng!
                </span>
                <x-input-error :messages="$errors->get('confirmation')" class="ms-2" />
            </label>

            <x-primary-button>
                Xác nhận
            </x-primary-button>
        </form>
    </div>
</x-app-layout>
