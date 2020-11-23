[33mcommit 454068f1705a536d32e3309af68560cb5500312d[m[33m ([m[1;36mHEAD -> [m[1;32maiyash_branch_repo[m[33m, [m[1;31morigin/aiyash_branch_repo[m[33m)[m
Author: Aiyash Ahmed <aiyashahmed96@gmail.com>
Date:   Wed Nov 18 14:37:38 2020 +0530

    make design changes in input fields

[33mcommit 83f1ea7ca7623282001aaa2da7e7fbf8f8d2cac5[m
Author: Aiyash Ahmed <aiyashahmed96@gmail.com>
Date:   Wed Nov 18 14:28:32 2020 +0530

    made changes in logut button

[33mcommit e897cd9c83050ba9b30ff533e9e9558c5d110459[m
Author: Aiyash Ahmed <aiyashahmed96@gmail.com>
Date:   Wed Nov 18 12:20:14 2020 +0530

    made changes in global variables

[33mcommit 8046ff7f209d75a34883ddbf72aa8d5db31b358a[m
Author: Aiyash Ahmed <aiyashahmed96@gmail.com>
Date:   Tue Nov 17 07:34:30 2020 +0530

    remove unwanted files

[33mcommit b968815a9afb595e7e6a28ba4ccf5490b34bad88[m
Author: Aiyash Ahmed <aiyashahmed96@gmail.com>
Date:   Sat Nov 14 14:31:43 2020 +0530

    added sql migration queries

[33mcommit 51656738c46033463356cbc27dc64e84ea15a5d4[m
Author: Aiyash Ahmed <aiyashahmed96@gmail.com>
Date:   Sat Nov 14 01:54:29 2020 +0530

    add database backup file

[33mcommit 8ea0a8c4c0393d4f5527e80fd9fe99172d6659ee[m
Author: Aiyash Ahmed <aiyashahmed96@gmail.com>
Date:   Sat Nov 14 01:46:33 2020 +0530

    added three new features

[33mcommit 05911cd079bb11e6f7dc4049b45c9a32ea83b126[m[33m ([m[1;31morigin/master[m[33m, [m[1;31morigin/HEAD[m[33m, [m[1;32mmaster[m[33m)[m
Author: stephen waite <stephen.waite@cmsvt.com>
Date:   Sat Oct 10 04:04:43 2020 -0400

    these chars seem to make everything bold and too big (#3962)

[33mcommit c3296716448a65b2fe4ca202641ce3100ef00d48[m
Author: Brady Miller <brady.g.miller@gmail.com>
Date:   Thu Oct 8 21:42:34 2020 -0700

    minor fix (#3963)

[33mcommit db62417bed68f3d144575eb06a7291d5cf0c3d3b[m
Author: stephen waite <stephen.waite@cmsvt.com>
Date:   Thu Oct 8 21:49:01 2020 -0400

    restore non-ICD10 unzip like for rxnorm (#3961)
    
    * restore non-ICD10 unzip like for rxnorm
    
    * phpcs roll back
    
    * roll on out :)

[33mcommit 878ad222aba86c155cf3f938ebe7d6f15bbbffda[m
Author: Brady Miller <brady.g.miller@gmail.com>
Date:   Tue Oct 6 20:00:58 2020 -0700

    fixes to prior commit

[33mcommit 375500557d890c66ab550683c7f3469045249247[m
Author: Cliff Su <stu01509@yahoo.com.tw>
Date:   Wed Oct 7 11:01:40 2020 +0800

    refactor: refine the fee section pages. (#3857)
    
    * refactor: refine the fee section pages.
    
    - Remove outdated code and inline-style.
    - Adjust the bootstrap grid system.
    - Align the field title to input field.
    
    * refactor: refine the receipt for payment page.
    
    - Remove outdated code and inline-style.
    - Add the bootstrap grid system, button and tabel style.
    
    * refactor: refine the fee > chceckout page.
    
    - Remove outdated code and inline-style.
    - Add the bootstrap button and table style.
    - Add missing html tag.
    
    * refactor: refine the receipt for payment.
    
    - Remove outdated code and inline-style.
    - Add the bootstrap table and button style.
    - Fix missing html tag.
    
    * refactor: remove outdated css code.
    
    * refactor: refine the billing manager page.
    
    - Refine the billing manager, day sheet report, and confirm page.
    - Remove outdated code and inline-style.
    - Convert the outdated style to the Bootstrap class.
    
    * refactor: refine the billing section pages.
    
    * refactor: remove outdated code.
    
    * refactor: remove outdated code.
    
    * feat: remove the hr tag outdated style.
    
    * style: fix psr12 style.
    
    * feat: adjust the page padding.

[33mcommit 5bac8f138c3be5b8b5d8da232d1b89d839ffca82[m
Author: stephen waite <stephen.waite@cmsvt.com>
Date:   Tue Oct 6 18:09:10 2020 -0400

    Priorauth (#3955)
    
    * remove from contrib, bootstrap it, add date from to fields
    
    * close input tags, bump version
    
    * weird indent
    
    * phpcbf
    
    * fix date formatting for i18n and remove backticks
    
    * remove redundant require of formatting.inc
    
    * date formatting and smarty syntax
    
    * phpcs

[33mcommit 2763b493f128161e6b77a392ba1e0fcb1c023b12[m
Author: vishnu yarmaneni <3436873+vishnuyar@users.noreply.github.com>
Date:   Wed Oct 7 01:41:14 2020 +0530

    corrected misspelling for system and added codeable concepts in FHIR  Condition (#3959)

[33mcommit 27134885b8b1a5bfe5653a4d64d4d47607fcfdf6[m
Author: vishnu yarmaneni <3436873+vishnuyar@users.noreply.github.com>
Date:   Tue Oct 6 23:07:12 2020 +0530

    Bug fix for FHIR Condition Resource in clinical status (#3958)

[33mcommit 04074ac27966b3064cd31295314a2ffd178415a6[m
Author: Brady Miller <brady.g.miller@gmail.com>
Date:   Tue Oct 6 01:35:32 2020 -0700

    minor fixes to prior commit

[33mcommit 8abadeaa9f0129c298babaad5b467ce24145a595[m
Author: Cliff Su <stu01509@yahoo.com.tw>
Date:   Tue Oct 6 16:30:59 2020 +0800

    Refactor: refine the fee sheet, new encounter form, procedure order pages. (#3845)
    
    * refactor: refine the fee sheet page.
    
    - Remove outdated code, comment, and inline-style.
    - Adjust the bootstrap form class.
    
    * fix: adjust the td width.
    
    * refactor: refine New Encounter Form and Procedure Order.
    
    - Add missing single quote.
    - Adjust the dialog footer button.
    - Fix the procedure order remove icon.
    - Remove outdated code and inline-style
    - Adjust the bootstrap grid system, form table style.
    
    * feat: remove odd style.
    
    * refactor: refine the procedure order page.
    
    - Remove outdated style code.
    - Replace the oe-text-to-right class to bootstrap text-right.
    - Adjust the diagnosis codes highlight style and js condition.
    
    * feat: adjust the css style.
    
    * feat: adjust the css style, replace the outdated spinner to bootstrap spinner.
    
    * fix: fix the procedure diagnosis codes modal.
    
    * feat: chagne the button style.
    
    * feat: adjust the page padding.
    
    * fix: add modal close button

[33mcommit 232ac095d54d93db89b5671edce58994a6db7e78[m
Author: vishnu yarmaneni <3436873+vishnuyar@users.noreply.github.com>
Date:   Tue Oct 6 11:15:13 2020 +0530

    Adding type in FHIR Encounter and Patient FHIR search by id (#3943)
    
    * converted to FHIR Datetime format
    
    * creating get api for insurance company by id
    
    * add code and reasoncode to FHIR Procedure
    
    * psr12style fixes
    
    * adding enconter type and patient search by id
    
    * removing getencountertype
    
    * fixed practitioner reference by uuid
    
    * added CreateMissingUuids for tables

[33mcommit 78ad099bbc395d3f6cda26a5270a3910b48328f8[m
Author: Brady Miller <brady.g.miller@gmail.com>
Date:   Mon Oct 5 22:41:34 2020 -0700

    php8 fixes and a couple other misc fixes (#3953)
    
    * php8 fixes and a couple other misc fixes
    
    * fix

[33mcommit 92fe4b5fa0832c7fed4d2a96c1f66e80e86e1498[m
Author: Brady Miller <brady.g.miller@gmail.com>
Date:   Sun Oct 4 22:11:11 2020 -0700

    package-lock.json update

[33mcommit 08ef078efb9d8e41f7311a175fe9ecfe6479959c[m
Author: Jerry Padgett <sjpadgett@gmail.com>
Date:   Sun Oct 4 19:11:18 2020 -0400

    V5tov6 ports (#3952)
    
    * fix reverted code and Travis
    
    * Revert "fix reverted code and Travis"
    
    This reverts commit 3cdfefe28430b4909eef97da23e843979a869453.
    
    * v5.0.2 to v6.0 ports
    - replace lbf layout options checkbox radio inputs from form-control class to form-check-inline
    - add LBF templating to portal
    - bump Dicom lib to 0.27.1
    
    * - address review comments
    - convert signer api to match new signer pad
    - change pads aspect ratio to better match new tabs styling

[33mcommit 51f155b5c3f54cc1f57edd260449ad4eb15a5b8e[m
Author: Cliff Su <stu01509@yahoo.com.tw>
Date:   Mon Oct 5 06:58:03 2020 +0800

    Refine Administration global page (#3945)
    
    * feat: adjust the button style
    
    * feat: sticky-top the sidebar.

[33mcommit 0351cb4ffb2b22323c0c7e5a110