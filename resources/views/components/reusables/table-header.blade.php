<thead class="bg-primary">
            <tr>
                @foreach ($tableHead as $head)
                    <th
                        class="whitespace-nowrap bg-primary  font-semibold text-white uppercase  dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                        {{$head}}
                    </th>
                @endforeach
            </tr>
</thead>
